<?php

namespace Apitizer\Types\Concerns;

trait HasDescription
{
    /**
     * @var string
     */
    protected $description;

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
