<?php

namespace Tests\Feature;

use Apitizer\Apitizer;
use Apitizer\Schema;
use Apitizer\Types\Apidoc;
use Apitizer\Types\ApidocCollection;

class ApitizerTest extends TestCase
{
    /** @test */
    public function it_returns_a_collection_of_api_documentation()
    {
        $apidoc = Apitizer::getQueryBuilderDocumentation();

        $this->assertInstanceOf(ApidocCollection::class, $apidoc);
        $this->assertCount(count(app(Schema::class)->getQueryBuilders()), $apidoc);

        $classes = [];
        foreach ($apidoc as $doc) {
            $this->assertInstanceOf(Apidoc::class, $doc);
            $classes[] = get_class($doc->getQueryBuilder());
        }

        $this->assertEquals(Apitizer::schema()->getQueryBuilders(), $classes);
    }
}
