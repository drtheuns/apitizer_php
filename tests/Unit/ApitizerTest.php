<?php

namespace Tests\Feature;

use Apitizer\Apitizer;
use Apitizer\Types\Apidoc;
use Apitizer\Types\ApidocCollection;
use Tests\Feature\Builders;

class ApitizerTest extends TestCase
{
    protected $builderClasses;

    protected function getEnvironmentSetUp($app)
    {
        $this->builderClasses = [
            Builders\PostBuilder::class,
            Builders\CommentBuilder::class,
            Builders\UserBuilder::class,
        ];

        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__ . '/../../config/apitizer.php');
        $app['config']->set('apitizer.query_builders', $this->builderClasses);
    }

    /** @test */
    public function it_returns_a_collection_of_api_documentation()
    {
        $apidoc = Apitizer::getQueryBuilderDocumentation();

        $this->assertInstanceOf(ApidocCollection::class, $apidoc);
        $this->assertCount(3, $apidoc);

        $classes = [];
        foreach ($apidoc as $doc) {
            $this->assertInstanceOf(Apidoc::class, $doc);
            $classes[] = get_class($doc->getQueryBuilder());
        }

        $this->assertEquals($this->builderClasses, $classes);
    }
}
