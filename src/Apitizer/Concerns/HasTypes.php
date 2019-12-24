<?php

namespace Apitizer\Concerns;

use Apitizer\FieldType;
use Apitizer\FieldTypes\AnyType;
use Apitizer\FieldTypes\StringType;
use Apitizer\FieldTypes\IntegerType;

trait HasTypes
{
    protected function makeType(string $key, string $type): FieldType
    {
        $type = new $type();
        $type->setKey($key);

        return $type;
    }

    protected function any(string $key): AnyType
    {
        return $this->makeType($key, AnyType::class);
    }

    protected function string(string $key): StringType
    {
        return $this->makeType($key, StringType::class);
    }

    protected function int(string $key): IntegerType
    {
        return $this->makeType($key, IntegerType::class);
    }
}
