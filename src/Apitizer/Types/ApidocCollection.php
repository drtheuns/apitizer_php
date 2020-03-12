<?php

namespace Apitizer\Types;

use Illuminate\Support\Collection;

class ApidocCollection extends Collection
{
    /**
     * @param string[] $schemas
     *
     * @return ApidocCollection<Apidoc>
     */
    public static function forSchemas(array $schemas): ApidocCollection
    {
        $collection = [];

        foreach ($schemas as $schemaClass) {
            $collection[$schemaClass] = new Apidoc(new $schemaClass);
        }

        return new static($collection);
    }

    public function findAssociationType(Association $assoc): ?Apidoc
    {
        $schema = \get_class($assoc->getRelatedSchema());

        return $this->items[$schema] ?? null;
    }
}
