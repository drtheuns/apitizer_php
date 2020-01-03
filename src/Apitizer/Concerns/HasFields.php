<?php

namespace Apitizer\Concerns;

use Apitizer\Types\Field;
use Apitizer\Types\DateTimeField;
use Apitizer\Transformers\CastValue;

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

    protected function float(string $key): Field
    {
        return $this->field($key, 'float')->transform(new CastValue);
    }

    protected function date(string $key): DateTimeField
    {
        return (new DateTimeField($this, $key, 'date'))
            ->transform(new CastValue);
    }

    protected function datetime(string $key): DateTimeField
    {
        return (new DateTimeField($this, $key, 'datetime'))
            ->transform(new CastValue);
    }
}
