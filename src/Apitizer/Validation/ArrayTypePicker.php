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
        $type = new StringRules(null);

        $this->setElementType($type);

        return $type;
    }

    public function uuid(): StringRules
    {
        return $this->string()->uuid();
    }

    public function boolean(): BooleanRules
    {
        $type = new BooleanRules(null);

        $this->setElementType($type);

        return $type;
    }

    public function date(string $format = null): DateRules
    {
        $type = DateRules::date(null, $format);

        $this->setElementType($type);

        return $type;
    }

    public function datetime(string $format = null): DateRules
    {
        $type = DateRules::datetime(null, $format);

        $this->setElementType($type);

        return $type;
    }

    public function number(): NumberRules
    {
        $type = new NumberRules(null);

        $this->setElementType($type);

        return $type;
    }

    public function integer(): NumberRules
    {
        $type = new IntegerRules(null);

        $this->setElementType($type);

        return $type;
    }

    public function file(): FileRules
    {
        $type = new FileRules(null);

        $this->setElementType($type);

        return $type;
    }

    public function image(): FileRules
    {
        return $this->file()->image();
    }

    public function array(): ArrayRules
    {
        $type = new ArrayRules(null);

        $this->setElementType($type);

        return $type;
    }

    public function object(Closure $callback): ObjectRules
    {
        $type = new ObjectRules(null, $callback);

        $this->setElementType($type);

        return $type;
    }

    private function setElementType(TypedRuleBuilder $type): void
    {
        $this->origin->setElementType($type);
    }
}
