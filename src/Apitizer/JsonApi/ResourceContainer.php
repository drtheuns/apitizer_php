<?php

namespace Apitizer\JsonApi;

use Illuminate\Support\Collection;

class ResourceContainer extends Collection implements Resource
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $id
     * @param string $type
     * @param array<int, array<string, mixed>>|array<string, mixed> $data
     */
    public function __construct(string $id, string $type, array $data)
    {
        $this->id = $id;
        $this->type = $type;
        $this->items = $data;
    }

    public function getResourceId(): string
    {
        return $this->id;
    }

    public function getResourceType(): string
    {
        return $this->type;
    }
}
