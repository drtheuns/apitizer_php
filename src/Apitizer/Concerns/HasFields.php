<?php

namespace Apitizer\Concerns;

use Apitizer\Types\Field;
use Apitizer\Types\EnumField;
use Apitizer\Types\DateTimeField;
use Apitizer\Transformers\CastValue;
use Apitizer\Types\GeneratedField;

trait HasFields
{
    protected function field(string $key, string $type): Field
    {
        return new Field($this, $key, $type);
    }

    protected function any(string $key): Field
    {
        return $this->field($key, 'any');
    }

    protected function string(string $key): Field
    {
        return $this->field($key, 'string')->transform(new CastValue);
    }

    protected function int(string $key): Field
    {
        return $this->field($key, 'int')->transform(new CastValue);
    }

    protected function uuid(string $key): Field
    {
        return $this->field($key, 'uuid')->transform(new CastValue);
    }

    protected function float(string $key): Field
    {
        return $this->field($key, 'float')->transform(new CastValue);
    }

    protected function boolean(string $key): Field
    {
        return $this->field($key, 'boolean')->transform(new CastValue);
    }

    protected function date(string $key, string $castFormat = null): DateTimeField
    {
        return (new DateTimeField($this, $key, 'date'))
            ->transform(new CastValue($castFormat));
    }

    protected function datetime(string $key, string $castFormat = null): DateTimeField
    {
        return (new DateTimeField($this, $key, 'datetime'))
            ->transform(new CastValue($castFormat));
    }

    /**
     * @param string $key
     * @param array<mixed> $enum
     * @param string $type
     */
    protected function enum(string $key, array $enum, string $type = 'string'): EnumField
    {
        return (new EnumField($this, $key, $enum, $type))
            ->transform(new CastValue);
    }

    protected function generatedField(string $type, callable $generator): GeneratedField
    {
        return new GeneratedField($this, $type, $generator);
    }
}
