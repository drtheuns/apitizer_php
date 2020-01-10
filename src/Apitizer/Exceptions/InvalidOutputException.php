<?php

namespace Apitizer\Exceptions;

use Apitizer\Types\EnumField;
use Apitizer\Types\Field;
use Illuminate\Database\Eloquent\Model;
use ArrayAccess;

/**
 * This exception occurs when there is a mismatch between the schema definition
 * and the data that was actually fetched from the database. For example, if a
 * field is set to not null but a null value is received nonetheless, this
 * exception will occur.
 *
 * The developers should be notified of these exceptions so that they may be fixed.
 */
class InvalidOutputException extends ApitizerException
{
    /**
     * The query builder where the exception occurred.
     *
     * @var QueryBuilder
     */
    public $queryBuilder;

    /**
     * The class from which the exception originates
     *
     * @var Field|EnumField
     */
    public $origin;

    public static function fieldIsNull(Field $field, $row): self
    {
        $fieldName = $field->getName();
        $queryBuilderName = get_class($field->getQueryBuilder());
        $reference = static::rowReference($row);
        $message = "Field [$fieldName] on [$queryBuilderName] is declared as not"
                 ." nullable but a null value was received for row [$reference]";

        $e = new static($message);
        $e->origin = $field;
        $e->queryBuilder = $field->getQueryBuilder();

        return $e;
    }

    public static function invalidEnum(EnumField $field, $value, $row): self
    {
        $fieldName = $field->getName();
        $queryBuilderName = get_class($field->getQueryBuilder());
        $reference = static::rowReference($row);
        $message = "Field [$fieldName] on [$queryBuilderName] received unexpected"
            . " enum value [$value] for row [$reference]";

        $e = new static($message);
        $e->origin = $field;
        $e->queryBuilder = $field->getQueryBuilder();

        return $e;
    }

    public static function castError(Field $field, CastException $e, $row)
    {
        $fieldName = $field->getName();
        $queryBuilderName = get_class($field->getQueryBuilder());
        $reference = static::rowReference($row);
        $value = $e->value;
        $type = $e->type;
        $message = "Field [$fieldName] on [$queryBuilderName] received a cast error"
                 . " when attempting to cast [$value] to [$type] for row [$reference]";

        $e = new static($message);
        $e->origin = $field;
        $e->queryBuilder = $field->getQueryBuilder();

        return $e;
    }

    /**
     * Do a best attempt at getting a reference to the object that cause an
     * exception.
     *
     * The entire row of data cannot be used because there might be sensitive
     * data in it that should not be logged outside of the system.
     */
    public static function rowReference($row): string
    {
        if ($row instanceof Model) {
            return (string) $row->getKey();
        }

        if ($row instanceof ArrayAccess || is_array($row)) {
            if (isset($row['id'])) {
                return (string) $row['id'];
            }

            if (isset($row['uuid'])) {
                return (string) $row['uuid'];
            }
        }

        if (is_object($row)) {
            if (isset($row->{'id'})) {
                return (string) $row->{'id'};
            }

            if (isset($row->{'uuid'})) {
                return (string) $row->{'uuid'};
            }
        }

        return 'could not generate reference';
    }
}
