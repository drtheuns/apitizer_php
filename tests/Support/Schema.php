<?php

namespace Tests\Support;

use Tests\Support\Builders;

class Schema extends \Apitizer\Schema
{
    protected function registerBuilders()
    {
        $this->register([
            Builders\PostBuilder::class,
            Builders\CommentBuilder::class,
            Builders\UserBuilder::class,
            Builders\TagBuilder::class,
        ]);
    }
}
