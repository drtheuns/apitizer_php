<?php

namespace Apitizer\Rendering;

use Apitizer\Schema;
use Apitizer\Types\FetchSpec;

class ReferenceMapRenderer extends JsonApiRenderer
{
    public function render(Schema $schema, $data, FetchSpec $fetchSpec): array
    {
        $render = [];
        $render['data'] = $this->doRender(
            $schema, $data,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations()
        );

        $render['included'] = $this->included;

        $this->included = [];

        return $render;
    }
}
