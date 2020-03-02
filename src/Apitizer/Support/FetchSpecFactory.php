<?php

namespace Apitizer\Support;

use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Parser\ParsedInput;
use Apitizer\Parser\Relation;
use Apitizer\QueryBuilder;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\Sort;
use Apitizer\Types\Filter;

/**
 * Helper factory to build fetch specifications.
 */
class FetchSpecFactory
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var ParsedInput
     */
    protected $input;

    public function __construct(QueryBuilder $queryBuilder, ParsedInput $input)
    {
        $this->queryBuilder = $queryBuilder;
        $this->input = $input;
    }

    public static function fromRequestInput(
        ParsedInput $input,
        QueryBuilder $queryBuilder
    ): FetchSpec {
        return (new static($queryBuilder, $input))->make();
    }

    public function make(): FetchSpec
    {
        $fields = $this->selectedFields($this->queryBuilder, $this->input->fields);
        $associations = $this->selectedAssociations(
            $this->queryBuilder,
            $this->input->associations,
            $this->input->fields
        );

        // If nothing was (correct) was selected, return all the default fields
        // but no associations.
        if (empty($fields) && empty($associations)) {
            $fields = $this->queryBuilder->getFields();
        }

        $sorts = $this->selectedSorting();
        $filters = $this->selectedFilters();

        return new FetchSpec($fields, $associations, $sorts, $filters);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string[] $requestedFields
     *
     * @return AbstractField[]
     */
    protected function selectedFields(QueryBuilder $queryBuilder, array $requestedFields): array
    {
        $availableFields = $queryBuilder->getFields();
        /** @var AbstractField[] $selectedFields */
        $selectedFields = [];

        foreach ($requestedFields as $field) {
            if (is_string($field) && isset($availableFields[$field])) {
                $selectedFields[] = $availableFields[$field];
            }
        }

        return $selectedFields;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Relation[] $relations
     * @param string[] $fields
     *
     * @return Association[]
     */
    protected function selectedAssociations(
        QueryBuilder $queryBuilder,
        array $relations,
        array $fields
    ): array {
        $availableAssociations = $queryBuilder->getAssociations();
        $selectedAssociations = [];

        // Recursively select associations and their fields as specified by the
        // client.
        foreach ($relations as $relation) {
            if (isset($availableAssociations[$relation->name])) {
                $association = $availableAssociations[$relation->name];
                $relatedBuilder = $association->getRelatedQueryBuilder();

                $association->setFields(
                    $this->selectedFields($relatedBuilder, $relation->fields)
                );
                $association->setAssociations(
                    $this->selectedAssociations(
                        $relatedBuilder,
                        $relation->associations,
                        $relation->fields
                    )
                );

                if (empty($association->getFields()) && empty($association->getAssociations())) {
                    $association->setFields($relatedBuilder->getFields());
                }

                $selectedAssociations[] = $association;
            }
        }

        // Additionally, merge in any associations that were select using only
        // their name. For these associations only the fields will be selected.
        foreach ($fields as $field) {
            if (is_string($field) && isset($availableAssociations[$field])) {
                $association = $availableAssociations[$field];
                $association->setFields($association->getRelatedQueryBuilder()->getFields());
                $selectedAssociations[] = $association;
            }
        }

        return $selectedAssociations;
    }

    /**
     * @return Sort[]
     */
    protected function selectedSorting(): array
    {
        $availableSorting = $this->queryBuilder->getSorts();
        $selectedSorting = [];

        foreach ($this->input->sorts as $parserSort) {
            if (isset($availableSorting[$parserSort->getField()])) {
                $sort = $availableSorting[$parserSort->getField()];
                $sort->setOrder($parserSort->getOrder());
                $selectedSorting[] = $sort;
            }
        }

        return $selectedSorting;
    }

    /**
     * @return Filter[]
     */
    protected function selectedFilters(): array
    {
        $availableFilters = $this->queryBuilder->getFilters();
        $selectedFilters = [];

        foreach ($this->input->filters as $name => $filterInput) {
            try {
                if (isset($availableFilters[$name])) {
                    $filter = $availableFilters[$name];
                    $filter->setValue($filterInput);
                    $selectedFilters[] = $filter;
                }
            } catch (InvalidInputException $e) {
                $this->queryBuilder->getExceptionStrategy()->handle(
                    $this->queryBuilder,
                    $e
                );
            }
        }

        return $selectedFilters;
    }
}
