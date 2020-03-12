<?php

namespace Apitizer\Support;

use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Parser\ParsedInput;
use Apitizer\Parser\Relation;
use Apitizer\Schema;
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
     * @var Schema
     */
    protected $schema;

    /**
     * @var ParsedInput
     */
    protected $input;

    public function __construct(Schema $schema, ParsedInput $input)
    {
        $this->schema = $schema;
        $this->input = $input;
    }

    public static function fromRequestInput(
        ParsedInput $input,
        Schema $schema
    ): FetchSpec {
        return (new static($schema, $input))->make();
    }

    public function make(): FetchSpec
    {
        $fields = $this->selectedFields($this->schema, $this->input->fields);
        $associations = $this->selectedAssociations(
            $this->schema,
            $this->input->associations,
            $this->input->fields
        );

        // If nothing was (correct) was selected, return all the default fields
        // but no associations.
        if (empty($fields) && empty($associations)) {
            $fields = $this->schema->getFields();
        }

        $sorts = $this->selectedSorting();
        $filters = $this->selectedFilters();

        return new FetchSpec($fields, $associations, $sorts, $filters);
    }

    /**
     * @param Schema $schema
     * @param string[] $requestedFields
     *
     * @return AbstractField[]
     */
    protected function selectedFields(Schema $schema, array $requestedFields): array
    {
        $availableFields = $schema->getFields();
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
     * @param Schema $schema
     * @param Relation[] $relations
     * @param string[] $fields
     *
     * @return Association[]
     */
    protected function selectedAssociations(
        Schema $schema,
        array $relations,
        array $fields
    ): array {
        $availableAssociations = $schema->getAssociations();
        $selectedAssociations = [];

        // Recursively select associations and their fields as specified by the
        // client.
        foreach ($relations as $relation) {
            if (isset($availableAssociations[$relation->name])) {
                $association = $availableAssociations[$relation->name];
                $relatedBuilder = $association->getRelatedSchema();

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
                $association->setFields($association->getRelatedSchema()->getFields());
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
        $availableSorting = $this->schema->getSorts();
        $selectedSorting = [];

        foreach ($this->input->sorts as $parserSort) {
            if (isset($availableSorting[$parserSort->getField()])) {
                $sort = $availableSorting[$parserSort->getField()];
                $sort->setOrder($parserSort->getOrder());
                $selectedSorting[] = $sort;
            } else {
                $this->schema->getExceptionStrategy()->handle(
                    $this->schema,
                    InvalidInputException::undefinedSortCalled($parserSort->getField(), $this->schema)
                );
            }
        }

        return $selectedSorting;
    }

    /**
     * @return Filter[]
     */
    protected function selectedFilters(): array
    {
        $availableFilters = $this->schema->getFilters();
        $selectedFilters = [];

        foreach ($this->input->filters as $name => $filterInput) {
            try {
                if (isset($availableFilters[$name])) {
                    $filter = $availableFilters[$name];
                    $filter->setValue($filterInput);
                    $selectedFilters[] = $filter;
                } else {
                    $this->schema->getExceptionStrategy()->handle(
                        $this->schema,
                        InvalidInputException::undefinedFilterCalled($name, $this->schema)
                    );
                }
            } catch (InvalidInputException $e) {
                $this->schema->getExceptionStrategy()->handle(
                    $this->schema,
                    $e
                );
            }
        }

        return $selectedFilters;
    }
}
