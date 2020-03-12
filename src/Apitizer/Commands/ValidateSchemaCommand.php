<?php

namespace Apitizer\Commands;

use Apitizer\Exceptions\DefinitionException;
use Apitizer\Schema;
use Illuminate\Console\Command;
use Apitizer\Support\SchemaValidator;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ValidateSchemaCommand extends Command
{
    protected $signature = 'apitizer:validate-schema { schemaClass? : the fully qualified class name of the schema to check }';
    protected $description = 'Validate the schemas';

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
        /** @var string $schemaClass */
        $schemaClass = $this->argument('schemaClass');

        if ($schemaClass) {
            if (! class_exists($schemaClass)) {
                $this->error("The given class [$schemaClass] could not be found");
                return 1;
            }

            $schema = new $schemaClass();

            if (! $schema instanceof Schema) {
                $this->error("The given class [$schemaClass] is not a schema");
                return 1;
            }

            $this->schemaValidator->validate($schema);
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
            return get_class($e->getSchema());
        })->sortKeys();

        $definitionErrors->each(function (Collection $errors, string $schemaClass) {
            $this->section($schemaClass);

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
