<?php

namespace Apitizer\Validation;

use Apitizer\Validation\Rules\AfterOrEqualRule;
use Apitizer\Validation\Rules\AfterRule;
use Apitizer\Validation\Rules\BeforeOrEqualRule;
use Apitizer\Validation\Rules\BeforeRule;
use Apitizer\Validation\Rules\DateEqualsRule;
use Apitizer\Validation\Rules\DateFormatRule;
use DateTimeInterface;

class DateRules extends FieldRuleBuilder
{
    use Concerns\SharedRules;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $type;

    public function __construct(?string $fieldName, string $format, string $type = 'date')
    {
        parent::__construct($fieldName);
        $this->format = $format;
        $this->type = $type;
    }

    public static function date(?string $fieldName, string $format = null): DateRules
    {
        return new static($fieldName, $format ?? config('apitizer.validation.date_format'));
    }

    public static function datetime(?string $fieldName, string $format = null): DateRules
    {
        return new static(
            $fieldName,
            $format ?? config('apitizer.validation.datetime_format'),
            'datetime'
        );
    }

    public function after(DateTimeInterface $date): self
    {
        return $this->addRule(new AfterRule($date, $this->format));
    }

    public function afterOrEqual(DateTimeInterface $date): self
    {
        return $this->addRule(new AfterOrEqualRule($date, $this->format));
    }

    public function before(DateTimeInterface $date): self
    {
        return $this->addRule(new BeforeRule($date, $this->format));
    }

    public function beforeOrEqual(DateTimeInterface $date): self
    {
        return $this->addRule(new BeforeOrEqualRule($date, $this->format));
    }

    public function equals(DateTimeInterface $date): self
    {
        return $this->addRule(new DateEqualsRule($date, $this->format));
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValidatableType()
    {
        return $this->getType() === 'date'
            ? 'date'
            : (new DateFormatRule($this->format))->toValidationRule();
    }
}
