<?php

namespace Apitizer\Commands;

use Apitizer\Exceptions\DefinitionException;
use Apitizer\QueryBuilder;
use Illuminate\Console\Command;
use Apitizer\Support\SchemaValidator;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ValidateSchemaCommand extends Command
{
    protected $signature = 'apitizer:validate-schema { builderClass? : the fully qualified class name of the builder to check }';
    protected $description = 'Validate the query builders';

    /**
     * @var SchemaValidator
     */
    protected $schemaValidator;

    public function __construct(SchemaValidator $schemaValidator)
    {
        parent::__construct();
        $this->schemaValidator = $schemaValidator;
    }

    public function handle(): ?int
    {
        /** @var string $builderClass */
        $builderClass = $this->argument('builderClass');

        if ($builderClass) {
            if (! class_exists($builderClass)) {
                $this->error("The given class [$builderClass] could not be found");
                return 1;
            }

            $builder = new $builderClass();

            if (! $builder instanceof QueryBuilder) {
                $this->error("The given class [$builderClass] is not a query builder");
                return 1;
            }

            $this->schemaValidator->validate($builder);
        } else {
            $this->schemaValidator->validateAll();
        }

        if ($this->schemaValidator->hasErrors()) {
            $this->printErrors($this->schemaValidator);
            return 1;
        }

        $this->info('No errors found');
        return 0;
    }

    public function printErrors(SchemaValidator $schemaValidator): void
    {
        $errors = collect($schemaValidator->getErrors());

        /** @var \Illuminate\Support\Collection */
        list($definitionErrors, $unexpectedErrors) = $errors->partition(function ($e) {
            return $e instanceof DefinitionException;
        });

        if (($count = $unexpectedErrors->count()) > 0) {
            $this->section("$count unexpected errors occurred");

            $unexpectedErrors->each(function (Exception $e) {
                $this->printException($e);
            });

            $this->line("\n");
        }

        $definitionErrors = $definitionErrors->groupBy(function (DefinitionException $e) {
            return get_class($e->getQueryBuilder());
        })->sortKeys();

        $definitionErrors->each(function (Collection $errors, string $builderClass) {
            $this->section($builderClass);

            $errors = $errors->groupBy->getNamespace();
            foreach (DefinitionException::NAMESPACES as $namespace) {
                if (! isset($errors[$namespace])) {
                    continue;
                }

                $this->comment($this->listItem(Str::title($namespace)));
                /** @var DefinitionException $e */
                foreach ($errors[$namespace] as $e) {
                    $this->line($this->listItem($e->getMessage(), 2));
                }
            }
        });
    }

    private function printException(Exception $e): void
    {
        $file = $e->getFile();
        $line = $e->getLine();

        $this->line($this->listItem("[$file:$line]: {$e->getMessage()}"));
        $this->error($e->getTraceAsString(), 'v');
    }

    private function section(string $text): void
    {
        $this->comment($text);
        $this->comment(str_repeat('-', strlen($text)));
    }

    private function listItem(string $text, int $depth = 0): string
    {
        return str_repeat(' ', $depth) . '* ' . $text;
    }
}
