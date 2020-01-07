<?php

namespace Apitizer\Support;

use DateTime;
use DateTimeInterface;

class TypeCaster
{
    public static function cast($value, string $type, ?string $format = null)
    {
        // Null values should evaluate to "false" in the boolean cast,
        // which is why this check comes before the null check.
        if ($type === 'bool' || $type === 'boolean') {
            return (bool) \filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

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

        throw new \UnexpectedValueException('Unable to cast ' . gettype($value) . ' to string');
    }
}
