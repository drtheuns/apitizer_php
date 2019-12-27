<?php

namespace Apitizer;

class Apitizer
{
    public static function getQueryBuilders()
    {
        return config('apitizer.query_builders', []);
    }

    public static function getFieldKey()
    {
        return config('apitizer.query_parameters.fields');
    }

    public static function getSortKey()
    {
        return config('apitizer.query_parameters.sort');
    }

    public static function getFilterKey()
    {
        return config('apitizer.query_parameters.filters');
    }
}
