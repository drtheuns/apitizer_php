<?php

namespace Apitizer\Support;

use Apitizer\Validation\ArrayRules;
use Apitizer\Validation\ObjectRules;
use Apitizer\Validation\TypedRuleBuilder;

/**
 * Helper for the typescript interfaces in the doc view.
 */
class TsViewHelper
{
    public static function printableType(TypedRuleBuilder $field, int $depth)
    {
        if ($field instanceof ArrayRules) {
            $elementType = $field->getElementType();

            return static::printableType($elementType, $depth) . '[]';
        }

        $type = $field->getType();

        return $type === 'object'
            ? static::printObject($field, $depth + 1)
            : static::toTsType($type);
    }

    public static function printObject(ObjectRules $object, $depth)
    {
        return view('apitizer::ts_object', ['builder' => $object, 'depth' => $depth]);
    }

    public static function toTsType(?string $type)
    {
        if (! $type) {
            return 'any';
        }

        switch ($type) {
            case 'date':
            case 'datetime':
                return 'Date';
            case 'integer':
                return 'number';
            default:
                return $type;
        }
    }
}
