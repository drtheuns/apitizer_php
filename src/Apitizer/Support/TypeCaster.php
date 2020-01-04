<?php

namespace Apitizer\Support;

use DateTime;
use DateTimeInterface;

class TypeCaster
{
    public static function cast($value, string $type, ?string $format = null)
    {
        if (is_null($value)) {
            return $value;
        }

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
            return self::castToDate($value, $format ?? 'Y-m-d');
        case 'datetime':
            return self::castToDate($value, $format ?? 'Y-m-d H:i:s');
        default:
            return $value;
        }
    }

    public static function castToDate($value, $format): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            $datetime = DateTime::createFromFormat($format, $value);

            // createFromFormat may return false if it fails.
            return $datetime ? $datetime : null;
        }

        if (is_null($value)) {
            return $value;
        }

        throw new \UnexpectedValueException('Unable to cast ' . gettype($value) . ' to string');
    }
}
