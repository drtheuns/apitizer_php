<?php

namespace Apitizer\Types;

use Illuminate\Support\Collection;

class ApidocCollection extends Collection
{
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
                ? $name . '[]'
                : $name;
        }

        return '';
    }
}
