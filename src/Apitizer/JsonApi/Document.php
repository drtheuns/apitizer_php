<?php

namespace Apitizer\JsonApi;

class Document
{
    // /** @var ResourceObject[] */
    // protected $includedResources = [];

    /** @var ResourceObject[] */
    protected $resources = [];

    /**
     * add a resource to the collection
     * adds included resources if found inside the resource's relationships, unless $options['includeContainedResources'] is set to false
     * @param string     $type
     * @param string $id
     * @param array      $attributes optional, if given a ResourceObject is added, otherwise a ResourceIdentifierObject is added
     */
    public function addResource(string $type, string $id, array $attributes=[]): void
    {
        $object = ResourceObject::factory($attributes, $type, $id);

        $this->resources[] = $object;
    }

    // /**
    //  * @inheritDoc
    //  */
    // public function setPaginationLinks($previousHref=null, $nextHref=null, $firstHref=null, $lastHref=null) {
    // 	if ($previousHref !== null) {
    // 		$this->addLink('prev', $previousHref);
    // 	}
    // 	if ($nextHref !== null) {
    // 		$this->addLink('next', $nextHref);
    // 	}
    // 	if ($firstHref !== null) {
    // 		$this->addLink('first', $firstHref);
    // 	}
    // 	if ($lastHref !== null) {
    // 		$this->addLink('last', $lastHref);
    // 	}
    // }

    public function toArray(): array
    {
        $data = [
            'data' => []
        ];

        foreach ($this->resources as $resource) {
            $data['data'][] = $resource->toArray();
        }

        return $data;
    }
}
