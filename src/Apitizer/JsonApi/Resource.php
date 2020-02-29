<?php

namespace Apitizer\JsonApi;

/**
 * An interface that can be used on Eloquent models to tweak the output of that
 * model in JsonApi responses.
 */
interface Resource
{
    /**
     * Get the value that should be used for the "type" field.
     */
    public function getResourceType(): string;

    /**
     * Get the value that should be used for the "id" field.
     */
    public function getResourceId(): string;
}
