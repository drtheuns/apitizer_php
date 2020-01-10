<?php

namespace Apitizer\Types;

use Illuminate\Support\Collection;

class ApidocCollection extends Collection
{
    /**
     * @param string[] $builders
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
        $builder = \get_class($assoc->getQueryBuilder());

        return $this->items[$builder] ?? null;
    }

    /**
     * @return \ArrayIterator|Apidoc[]
     */
    public function getIterator()
    {
        // Only override this for IDE type-hinting.
        // See as example https://github.com/symfony/symfony/issues/16965
        return parent::getIterator();
    }

}
