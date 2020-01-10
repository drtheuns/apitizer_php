<?php

namespace Apitizer\Support;

use Apitizer\Apitizer;
use Apitizer\QueryBuilder;
use Apitizer\Exceptions\DefinitionException;
use Exception;
use Closure;

class SchemaValidator
{
    /**
     * @var (DefinitionException|Exception)[]
     */
    protected $errors = [];

    /**
     * Validates all the query builders that Apitizer knows about.
     *
     * Requires the query builders to be defined in the config.
     *
     * @param null|QueryBuilder[] the list of query builders to validate.
     */
    public function validateAll(array $queryBuilders = null): self
    {
        $queryBuilders = $queryBuilders ?? Apitizer::getQueryBuilders();

        foreach (Apitizer::getQueryBuilders() as $queryBuilder) {
            $this->validate($queryBuilder);
        }

        return $this;
    }

    public function validate(QueryBuilder $queryBuilder): self
    {
        $this->catchAll(function () use ($queryBuilder) {
            $this->validateFields($queryBuilder);
        });
        $this->catchAll(function () use ($queryBuilder) {
            $this->validateFilters($queryBuilder);
        });
        $this->catchAll(function () use ($queryBuilder) {
            $this->validateSorting($queryBuilder);
        });

        return $this;
    }

    public function validateFields(QueryBuilder $queryBuilder): void
    {
        // Associations can fail the moment fields() is called.
        foreach ($queryBuilder->fields() as $name => $field) {
            try {
                DefinitionHelper::validateField($queryBuilder, $name, $field);
            } catch (DefinitionException $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function validateFilters(QueryBuilder $queryBuilder): void
    {
        foreach ($queryBuilder->filters() as $name => $filter) {
            try {
                DefinitionHelper::validateFilter($queryBuilder, $name, $filter);
            } catch (DefinitionException $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function validateSorting(QueryBuilder $queryBuilder): void
    {
        foreach ($queryBuilder->sorts() as $name => $sort) {
            try {
                DefinitionHelper::validateSort($queryBuilder, $name, $sort);
            } catch (DefinitionException $e) {
                $this->errors[] = $e;
            }
        }
    }

    private function catchAll(Closure $callback): void
    {
        // If something unexpected happens, add the error and continue
        // validating.
        try {
            call_user_func($callback);
        } catch (Exception $e) {
            $this->errors[] = $e;
        }
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    /**
     * @return (Exception|DefinitionException)[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
