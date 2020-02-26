<?php

namespace Apitizer\Validation;

use Apitizer\Exceptions\IncompleteRuleException;
use Illuminate\Support\Collection;

class ArrayValueRuleBuilder
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var TypedRuleBuilder
     */
    protected $valueBuilder;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function string()
    {
        return $this->setValueBuilder(new StringRuleBuilder($this->getName()));
    }

    public function uuid()
    {
        return $this->string()->uuid();
    }

    public function boolean()
    {
        return $this->setValueBuilder(new BooleanRuleBuilder($this->getName()));
    }

    public function date()
    {
        return $this->setValueBuilder(new DateRuleBuilder($this->getName()));
    }

    public function datetime()
    {
        return $this->setValueBuilder(new DateTimeRuleBuilder($this->getName()));
    }

    public function array()
    {
        return $this->setValueBuilder(new ArrayRuleBuilder($this->getName()));
    }

    public function number()
    {
        return $this->setValueBuilder(new NumberRuleBuilder($this->getName()));
    }

    public function integer()
    {
        return $this->setValueBuilder(new IntegerRuleBuilder($this->getName()));
    }

    public function file()
    {
        return $this->setValueBuilder(new FileRuleBuilder($this->getName()));
    }

    public function setValueBuilder(TypedRuleBuilder $type)
    {
        $this->valueBuilder = $type;

        return $type;
    }

    public function image()
    {
        return $this->file()->image();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        throw IncompleteRuleException::arrayTypeExpected();
    }

    public function addValidationRulesTo(Collection $rules): Collection
    {
        return $this->valueBuilder->addValidationRulesTo($rules);
    }

    public function addDocumentationTo(Collection $collection): Collection
    {
        return $collection->push($this);
    }
}
