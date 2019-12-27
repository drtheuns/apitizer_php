<?php

namespace Apitizer;

class Apitizer
{
    public static function getQueryBuilders()
    {
        return config('apitizer.query_builders', []);
    }
}
