<?php

namespace Tests\Feature\Commands;

use Apitizer\SchemaLoader;
use Tests\Feature\TestCase;
use Tests\Support\Schemas\EmptySchema;
use Tests\Support\Schemas\UserSchema;
use Mockery\MockInterface;

class ValidateSchemaCommandTest extends TestCase
{
    /** @test */
    public function it_validates_all_registered_schemas()
    {
        $this->mock(SchemaLoader::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSchemas')
                 ->once()
                 ->andReturn([
                     AssociationDoesNotExist::class,
                 ]);
        });

        $this->artisan('apitizer:validate-schema')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_validates_specific_schemas()
    {
        $class = AssociationDoesNotExist::class;
        $command = $this->artisan('apitizer:validate-schema', [
            'schemaClass' => $class,
        ]);

        $header = $class;
        $line = str_repeat('-', strlen($class));
        $namespace = '* Association';
        $msg = "  * Association [geckos] on [$class]"
             .' refers to association [geckos] which does not exist on the model'
             .' [Tests\Feature\Models\User]';
        $command->expectsOutput($header)
                ->expectsOutput($line)
                ->expectsOutput($namespace)
                ->expectsOutput($msg)
                ->assertExitCode(1);
    }

    /** @test */
    public function it_warns_when_a_non_schema_was_passed_as_argument()
    {
        $class = NotASchema::class;
        $command = $this->artisan('apitizer:validate-schema', [
            'schemaClass' => $class,
        ]);

        $output = "The given class [$class] is not a schema";
        $command->expectsOutput($output)
                ->assertExitCode(1);
    }

    /** @test */
    public function it_warns_when_the_given_schema_class_cannot_be_found()
    {
        $class = 'NotASchema';
        $command = $this->artisan('apitizer:validate-schema', [
            'schemaClass' => $class,
        ]);

        $output = "The given class [$class] could not be found";
        $command->expectsOutput($output)
            ->assertExitCode(1);
    }

    /** @test */
    public function it_should_return_successful_if_no_errors_were_found()
    {
        $command = $this->artisan('apitizer:validate-schema', [
            'schemaClass' => UserSchema::class,
        ]);

        $command->expectsOutput('No errors found')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_should_list_any_unexpected_exceptions_that_occurred()
    {
        $class = UnexpectedExceptions::class;
        $command = $this->artisan('apitizer:validate-schema', [
            'schemaClass' => $class,
        ]);

        // Testing for the error message is possible but fragile due to
        // file/line number in exception output.
        $command->expectsOutput('1 unexpected errors occurred')
                ->assertExitCode(1);
    }
}

class NotASchema
{
}
class AssociationDoesNotExist extends EmptySchema
{
    public function associations(): array
    {
        return [
            'geckos' => $this->association('geckos', UserSchema::class),
        ];
    }
}

class UnexpectedExceptions extends EmptySchema
{
    public function fields(): array
    {
        throw new \Exception('Totally unexpected');
        return [];
    }
}
