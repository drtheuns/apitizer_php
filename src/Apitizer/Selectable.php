<?php

namespace Apitizer;

interface Selectable
{
    /**
     * A function that returns the fields that are available to the client.
     *
     * The array that is returned should be map where the key is the name of the
     * field that will be used by the client, while the value is either a string
     * or a `FieldType`.
     *
     * If the value is a string, it will implicitly cast to the `AnyType`.
     *
     * Each type specifies a key that is used to fetch the data from the
     * eventual source data. In other words, if the query builder is used in
     * conjunction with a database and Eloquent, then the key would be the key
     * on the Eloquent model that should be used to fetch the data (usually the
     * column name in the database).
     */
    public function fields(): array;
}
