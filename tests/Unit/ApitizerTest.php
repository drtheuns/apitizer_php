<?php

namespace Tests\Unit;

use Apitizer\Apitizer;
use Apitizer\Types\Apidoc;
use Apitizer\Types\ApidocCollection;

class ApitizerTest extends TestCase
{
    /** @test */
    public function it_returns_a_collection_of_api_documentation()
    {
        $apidoc = Apitizer::getSchemaDocumentation();

        $this->assertInstanceOf(ApidocCollection::class, $apidoc);
        $this->assertCount(count($this->schemaClasses), $apidoc);

        $classes = [];
        foreach ($apidoc as $doc) {
            $this->assertInstanceOf(Apidoc::class, $doc);
            $classes[] = get_class($doc->getSchema());
        }

        $this->assertEquals(Apitizer::getSchemas(), $classes);
    }

    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }
}
