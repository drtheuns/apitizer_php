<?php

namespace Apitizer\Types;

use Illuminate\Support\Collection;

class ApidocCollection extends Collection
{
    /**
     * @return \ArrayIterator|Apidoc[]
     */
    public function getIterator()
    {
        // Only override this for IDE type-hinting.
        // See as example https://github.com/symfony/symfony/issues/16965
        return parent::getIterator();
    }

    public function findAssociationType(Association $assoc): ?Apidoc
    {
        $builder = $assoc->getQueryBuilder();

        foreach ($this->all() as $apidoc) {
            if (\get_class($apidoc->getQueryBuilder()) === \get_class($builder)) {
                return $apidoc;
            }
        }

        return null;
    }

    public function printAssociationType(Association $assoc): string
    {
        if ($apidoc = $this->findAssociationType($assoc)) {
            $name = $apidoc->getName();

            return $assoc->returnsCollection()
                ? "array of $name"
                : $name;
        }

        return '';
    }
}
