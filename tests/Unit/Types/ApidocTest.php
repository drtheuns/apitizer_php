<?php

namespace Tests\Unit\Types;

use Apitizer\Types\Apidoc;
use Tests\Support\Schemas\EmptySchema;
use Tests\Support\Schemas\UserSchema;
use Tests\Unit\TestCase;

class ApidocTest extends TestCase
{
    // The bindings are needed for this test.
    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }

    /** @test */
    public function it_should_guess_resource_names_from_the_schema()
    {
        // UserSchema -> User
        $schema = new Apidoc(new OrganizationSchema());
        $this->assertEquals('Organization', $schema->getName());
    }

    /** @test */
    public function it_falls_back_to_the_class_name_if_it_cannot_guess_the_name()
    {
        $apidoc = new Apidoc(new NonSchemaName());
        $this->assertEquals('NonSchemaName', $apidoc->getName());
    }

    /** @test */
    public function arbitrary_metadata_can_be_attached_to_the_documenation()
    {
        $apidoc = new Apidoc(new UserSchema());
        $apidoc->setMetadata(['deprecated' => true]);

        $this->assertEquals(['deprecated' => true], $apidoc->getMetadata());
    }
}

// Used as examples for name guessing.
class OrganizationSchema extends EmptySchema
{
}
class NonSchemaName extends EmptySchema
{
}
