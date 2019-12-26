<?php

namespace Apitizer\Support;

class TypeCaster
{
    public static function cast($value, string $type)
    {
        switch ($type) {
        case 'string':
            return (string) $value;
        case 'int':
        case 'integer':
            return (int) $value;
        case 'float':
        case 'double':
            return (float) $value;
        case 'bool':
        case 'boolean':
            return (bool) $value;
        case 'date':
            return $this->castToDate($value, 'Y-m-d');
        case 'datetime':
            return $this->castToDate($value, 'Y-m-d H:i:s');
        }
    }

    public static function castToDate($value, $format): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            return \DateTime::createFromFormat($value, $format);
        }

        if (is_null($value)) {
            return $value;
        }

        throw new \UnexpectedValueException('Unable to cast ' . gettype($value) . ' to string');
    }
}
