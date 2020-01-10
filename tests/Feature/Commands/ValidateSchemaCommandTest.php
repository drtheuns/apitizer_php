<?php

namespace Tests\Feature\Commands;

use Tests\Feature\TestCase;
use Tests\Feature\Builders\EmptyBuilder;
use Tests\Feature\Builders\UserBuilder;

class ValidateSchemaCommandTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $this->builderClasses = [
            AssociationDoesNotExist::class,
        ];

        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__ . '/../../../config/apitizer.php');
        $app['config']->set('apitizer.query_builders', $this->builderClasses);
    }

    /** @test */
    public function it_validates_all_registered_query_builders()
    {
        $this->artisan('apitizer:validate-schema')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_validates_specific_query_builders()
    {
        $class = AssociationDoesNotExist::class;
        $command = $this->artisan('apitizer:validate-schema', [
            'builderClass' => $class,
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
    public function it_warns_when_a_non_builder_was_passed_as_argument()
    {
        $class = NotABuilder::class;
        $command = $this->artisan('apitizer:validate-schema', [
            'builderClass' => $class,
        ]);

        $output = "The given class [$class] is not a query builder";
        $command->expectsOutput($output)
                ->assertExitCode(1);
    }

    /** @test */
    public function it_warns_when_the_given_builder_class_cannot_be_found()
    {
        $class = 'NotABuilder';
        $command = $this->artisan('apitizer:validate-schema', [
            'builderClass' => $class,
        ]);

        $output = "The given class [$class] could not be found";
        $command->expectsOutput($output)
            ->assertExitCode(1);
    }

    /** @test */
    public function it_should_return_successful_if_no_errors_were_found()
    {
        $command = $this->artisan('apitizer:validate-schema', [
            'builderClass' => UserBuilder::class,
        ]);

        $command->expectsOutput('No errors found')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_should_list_any_unexpected_exceptions_that_occurred()
    {
        $class = UnexpectedExceptions::class;
        $command = $this->artisan('apitizer:validate-schema', [
            'builderClass' => $class,
        ]);

        // Testing for the error message is possible but fragile due to
        // file/line number in exception output.
        $command->expectsOutput('1 unexpected errors occurred')
                ->assertExitCode(1);
    }
}

class NotABuilder{}
class AssociationDoesNotExist extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'geckos' => $this->association('geckos', UserBuilder::class),
        ];
    }
}

class UnexpectedExceptions extends EmptyBuilder
{
    public function fields(): array
    {
        throw new \Exception('Totally unexpected');
        return [];
    }
}
