<?php

namespace Apitizer\Support;

use Apitizer\Apitizer;
use Apitizer\Schema;
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
     * Validates all the schemas that Apitizer knows about.
     *
     * Requires the schemas to be defined in the config.
     *
     * @param null|(string|Schema)[] $schemas the list of query
     * builders to validate.
     */
    public function validateAll(array $schemas = null): self
    {
        foreach ($schemas ?? Apitizer::getSchemas() as $schema) {
            if (is_string($schema)) {
                $schema = new $schema;
            }

            $this->validate($schema);
        }

        return $this;
    }

    public function validate(Schema $schema): self
    {
        $this->catchAll(function () use ($schema) {
            $this->validateFields($schema);
        });
        $this->catchAll(function () use ($schema) {
            $this->validateAssociations($schema);
        });
        $this->catchAll(function () use ($schema) {
            $this->validateFilters($schema);
        });
        $this->catchAll(function () use ($schema) {
            $this->validateSorting($schema);
        });

        return $this;
    }

    public function validateFields(Schema $schema): void
    {
        // Associations can fail the moment fields() is called.
        foreach ($schema->fields() as $name => $field) {
            try {
                DefinitionHelper::validateField($schema, $name, $field);
            } catch (DefinitionException $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function validateAssociations(Schema $schema): void
    {
        foreach ($schema->associations() as $name => $field) {
            try {
                DefinitionHelper::validateAssociation($schema, $name, $field);
            } catch (DefinitionException $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function validateFilters(Schema $schema): void
    {
        foreach ($schema->filters() as $name => $filter) {
            try {
                DefinitionHelper::validateFilter($schema, $name, $filter);
            } catch (DefinitionException $e) {
                $this->errors[] = $e;
            }
        }
    }

    public function validateSorting(Schema $schema): void
    {
        foreach ($schema->sorts() as $name => $sort) {
            try {
                DefinitionHelper::validateSort($schema, $name, $sort);
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
