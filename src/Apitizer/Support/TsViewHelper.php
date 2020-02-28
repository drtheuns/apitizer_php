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
    /**
     * @return string
     */
    public static function printableType(TypedRuleBuilder $field, int $depth): string
    {
        if ($field instanceof ArrayRules) {
            $elementType = $field->getElementType();

            if ($elementType) {
                return static::printableType($elementType, $depth) . '[]';
            }

            return 'any[]';
        }

        return $field instanceof ObjectRules
            ? static::printObject($field, $depth + 1)
            : static::toTsType($field->getType());
    }

    public static function printObject(ObjectRules $object, int $depth): string
    {
        return view('apitizer::ts_object', ['builder' => $object, 'depth' => $depth]);
    }

    public static function toTsType(?string $type): string
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
