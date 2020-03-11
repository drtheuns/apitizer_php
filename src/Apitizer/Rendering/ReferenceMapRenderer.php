<?php

namespace Apitizer\Rendering;

use Apitizer\QueryBuilder;
use Apitizer\Types\FetchSpec;

class ReferenceMapRenderer extends JsonApiRenderer
{
    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    {
        $render = [];
        $render['data'] = $this->doRender(
            $queryBuilder, $data,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations()
        );

        $render['included'] = $this->included;

        $this->included = [];

        return $render;
    }
}
