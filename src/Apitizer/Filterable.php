<?php

namespace Apitizer;

interface Filterable
{
    /**
     * A function that returns the filters that are available to the client.
     */
    public function filters(): array;
}
