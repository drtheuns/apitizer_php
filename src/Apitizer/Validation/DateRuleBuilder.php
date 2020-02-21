<?php

namespace Apitizer\Validation;

use DateTimeInterface;

class DateRuleBuilder extends TypedRuleBuilder
{
    /**
     * @var string|null
     */
    protected $format;

    /**
     * Validate that the value occurs after the given date.
     *
     * @param DateTimeInterface|string $date
     */
    public function after($date): self
    {
        $date = $this->getDateString($date);

        return $this->addSimpleRule(
            'after', [$date],
            $this->trans('after', ['date' => $date])
        );
    }

    /**
     * Validate that the value occurs after or equal to the given date.
     *
     * @param DateTimeInterface|string $date
     */
    public function afterOrEqual($date): self
    {
        $date = $this->getDateString($date);

        return $this->addSimpleRule(
            'after_or_equal', [$date],
            $this->trans('after_or_equal', ['date' => $date])
        );
    }

    /**
     * Validate that the value occurs before the given date.
     *
     * @param DateTimeInterface|string $date
     */
    public function before($date): self
    {
        $date = $this->getDateString($date);

        return $this->addSimpleRule(
            'before', [$date],
            $this->trans('before', ['date' => $date])
        );
    }

    /**
     * Validate that the value occurs before or equal to the given date.
     *
     * @param DateTimeInterface|string $date
     */
    public function beforeOrEqual($date): self
    {
        $date = $this->getDateString($date);

        return $this->addSimpleRule(
            'before_or_equal', [$date],
            $this->trans('before_or_equal', ['date' => $date])
        );
    }

    /**
     * Validate that the value is equal to the given date.
     *
     * @param DateTimeInterface|string $date
     */
    public function equals($date): self
    {
        $date = $this->getDateString($date);

        return $this->addSimpleRule(
            'date_equals', [$date],
            $this->trans('date_equals', ['date' => $date])
        );
    }

    /**
     * Validate that the value equals the given date format.
     */
    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    protected function getDateString($datetime)
    {
        if ($datetime instanceof DateTimeInterface) {
            return $datetime->format($this->getDefaultFormat());
        }

        if (is_string($datetime)) {
            return $datetime;
        }

        // TODO:
        throw new \Exception();
    }

    public function getType(): string
    {
        return 'date';
    }

    protected function getTypeRule()
    {
        return 'date_format:' . ($this->format ?? $this->getDefaultFormat());
    }

    protected function getDefaultFormat()
    {
        return 'Y-m-d';
    }
}
