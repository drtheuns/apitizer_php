<?php

namespace Apitizer\JsonApi;

use ArrayAccess;

class ResourceContainer implements Resource, ArrayAccess
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
     * @var array<string, mixed>
     */
    protected $data;

    /**
     * @param string $type
     * @param string $id
     * @param array<string, mixed> $data
     */
    public function __construct(string $type, string $id, array $data)
    {
        $this->type = $type;
        $this->id = $id;
        $this->data = $data;
    }

    public function getResourceId(): string
    {
        return $this->id;
    }

    public function getResourceType(): string
    {
        return $this->type;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }
}
