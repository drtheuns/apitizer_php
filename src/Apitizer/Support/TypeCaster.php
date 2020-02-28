<?php

namespace Apitizer\Support;

use Apitizer\Exceptions\CastException;
use DateTime;
use DateTimeInterface;
use Exception;
use Ramsey\Uuid\Uuid;

class TypeCaster
{
    /**
     * @param mixed $value
     * @param string $type
     * @param string|null $format
     *
     * @throws CastException
     *
     * @return bool|int|float|string|DateTimeInterface|mixed|null
     */
    public static function cast($value, string $type, ?string $format = null)
    {
        try {
            return static::doCast($value, $type, $format);
        } catch (Exception $e) {
            if ($e instanceof CastException) {
                throw $e;
            }
            throw new CastException($value, $type, $format);
        }
    }

    /**
     * @param mixed $value
     * @param string $type
     * @param string|null $format
     *
     * @throws CastException
     *
     * @return bool|int|float|string|DateTimeInterface|mixed|null
     */
    private static function doCast($value, $type, $format)
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
            case 'uuid':
                return static::castUuid($value, $type, $format);
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'date':
                return self::castToDate($value, $type, $format ?? 'Y-m-d');
            case 'datetime':
                return self::castToDate($value, $type, $format ?? 'Y-m-d H:i:s');
            default:
                return $value;
        }
    }

    /**
     * @param mixed $value
     * @param string $type
     * @param string $format
     *
     * @throws CastException
     *
     * @return DateTimeInterface
     */
    private static function castToDate($value, $type, string $format): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            if ($datetime = DateTime::createFromFormat($format, $value)) {
                return $datetime;
            }
        }

        throw new CastException($value, $type, $format);
    }

    /**
     * @param mixed $value
     * @param string $type
     * @param string|null $format
     *
     * @throws CastException
     *
     * @return string
     */
    private static function castUuid($value, $type, $format): string
    {
        if ($value instanceof Uuid) {
            return $value;
        }

        $value = (string) $value;

        if (! Uuid::isValid($value)) {
            throw new CastException($value, $type, $format);
        }

        return $value;
    }
}
