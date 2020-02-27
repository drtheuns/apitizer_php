<?php

namespace Apitizer\Validation;

use Closure;

class ArrayTypePicker
{
    /**
     * @var ArrayRules the instance that spawned this type picker.
     */
    protected $origin;

    public function __construct(ArrayRules $origin)
    {
        $this->origin = $origin;
    }

    public function string(): StringRules
    {
        return $this->setElementType(new StringRules(null));
    }

    public function uuid(): StringRules
    {
        return $this->string()->uuid();
    }

    public function boolean(): BooleanRules
    {
        return $this->setElementType(new BooleanRules(null));
    }

    public function date(string $format = null): DateRules
    {
        return $this->setElementType(DateRules::date(null, $format));
    }

    public function datetime(string $format = null): DateRules
    {
        return $this->setElementType(DateRules::datetime(null, $format));
    }

    public function number(): NumberRules
    {
        return $this->setElementType(new NumberRules(null));
    }

    public function integer(): NumberRules
    {
        return $this->setElementType(new IntegerRules(null));
    }

    public function file(): FileRules
    {
        return $this->setElementType(new FileRules(null));
    }

    public function image(): FileRules
    {
        return $this->file()->image();
    }

    public function array()
    {
        return $this->setElementType(new ArrayRules(null));
    }

    public function object(Closure $callback)
    {
        return $this->setElementType(new ObjectRules(null, $callback));
    }

    private function setElementType(TypedRuleBuilder $type): TypedRuleBuilder
    {
        $this->origin->setElementType($type);

        return $type;
    }
}
