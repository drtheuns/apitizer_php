<?php

namespace Apitizer\Validation;

interface ContainerType
{
    /**
     * @return TypedRuleBuilder[]
     */
    public function getChildren(): array;

    /**
     * Resolve the current builder.
     */
    public function resolve();
}
