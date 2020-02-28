<?php

namespace Apitizer\Types;

use Illuminate\Support\Collection;

class ApidocCollection extends Collection
{
    /**
     * @param string[] $builders
     *
     * @return ApidocCollection<Apidoc>
     */
    public static function forQueryBuilders(array $builders): ApidocCollection
    {
        $collection = [];

        foreach ($builders as $builderClass) {
            $collection[$builderClass] = new Apidoc(new $builderClass);
        }

        return new static($collection);
    }

    public function findAssociationType(Association $assoc): ?Apidoc
    {
        $builder = \get_class($assoc->getRelatedQueryBuilder());

        return $this->items[$builder] ?? null;
    }
}
