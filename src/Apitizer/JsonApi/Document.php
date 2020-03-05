<?php

namespace Apitizer\JsonApi;

class Document
{
    // /** @var ResourceObject[] */
    // protected $includedResources = [];

    /** @var ResourceObject[] */
    protected $resources = [];

    /** @var array  */
    protected $includes = [];

    /**
     * add a resource to the collection
     * adds included resources if found inside the resource's relationships, unless $options['includeContainedResources'] is set to false.
     *
     * @param string $type
     * @param string $id
     * @param array  $attributes optional, if given a ResourceObject is added, otherwise a ResourceIdentifierObject is added
     */
    public function addResource(string $type, string $id, array $attributes = []): void
    {
        $object = ResourceObject::factory($attributes, $type, $id);

        $this->resources[] = $object;
    }

    public function toArray(): array
    {
        $resources = collect($this->resources);

        return $resources->count() == 1
            ? ['data' => $resources->first()->toArray()]
            : ['data' => $resources->toArray()];
    }
}
