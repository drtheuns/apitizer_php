<?php

namespace Apitizer;

interface Sortable
{
    /**
     * A function that returns the names of the sorting methods that are
     * available to the client.
     *
     * The following sorts:
     *
     *   ['name']
     *
     * would support the following queries:
     *
     *   /users?sort=name.asc
     */
    public function sorts(): array;
}
