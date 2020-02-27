<?php

namespace Apitizer\Validation;

use Apitizer\Validation\Rules\DigitsBetweenRule;
use Apitizer\Validation\Rules\DigitsRule;
use Apitizer\Validation\Rules\EmailRule;
use Apitizer\Validation\Rules\EndsWithRule;
use Apitizer\Validation\Rules\StartsWithRule;

class StringRules extends FieldRuleBuilder
{
    use Concerns\SharedRules;

    /**
     * Validate that the string is a url according contains a valid A or AAAA
     * record according to `dns_get_record`.
     */
    public function activeUrl(): self
    {
        return $this->addConstraint('active_url');
    }

    /**
     * Validate that the string consists only of alphabetic characters.
     */
    public function alpha(): self
    {
        return $this->addConstraint('alpha');
    }

    /**
     * Validate that the string consists only of alphanumeric characters,
     * dashes, and underscores.
     */
    public function alphaDash(): self
    {
        return $this->addConstraint('alpha_dash');
    }

    /**
     * Validate that the string consists only of alphanumeric characters.
     */
    public function alphaNum(): self
    {
        return $this->addConstraint('alpha_num');
    }

    /**
     * Validate that the string is numeric consisting of exactly $length digits.
     */
    public function digits(int $length): self
    {
        return $this->addRule(new DigitsRule($length));
    }

    /**
     * Validate that the string is numeric consisting between $min and $max digits.
     */
    public function digitsBetween(int $min, int $max): self
    {
        return $this->addRule(new DigitsBetweenRule($min, $max));
    }

    /**
     * Validate that the string is a valid email according to the
     * egulias/email-validator package.
     *
     * @param array $validationStyle the validation styles to apply. One of:
     *
     * - rfc: RFCValidation
     * - strict: NoRFCWarningsValidation
     * - dns: DNSCheckValidation
     * - spoof: SpoofCheckValidation
     * - filter: FilterEmailValidation
     */
    public function email(array $validationStyle = ['rfc']): self
    {
        return $this->addRule(new EmailRule($validationStyle));
    }

    /**
     * Validate that the string ends with one of the given values.
     *
     * @param string[] $values
     *
     * @see \Illuminate\Support\Str::endsWith
     */
    public function endsWith(array $values): self
    {
        return $this->addRule(new EndsWithRule($values));
    }

    /**
     * Validate that the string is a valid IP address.
     */
    public function ip(): self
    {
        return $this->addConstraint('ip');
    }

    /**
     * Validate that the string is a valid IPv4 address.
     */
    public function ipv4(): self
    {
        return $this->addConstraint('ipv4');
    }

    /**
     * Validate that the string is a valid IPv6 address.
     */
    public function ipv6(): self
    {
        return $this->addConstraint('ipv6');
    }

    /**
     * Validate that the string is a valid json string.
     */
    public function json(): self
    {
        return $this->addConstraint('json');
    }

    /**
     * Validate that the string is numeric.
     */
    public function numeric(): self
    {
        return $this->addConstraint('numeric');
    }

    /**
     * Validate that the string starts with one of the given values.
     *
     * @param string[] $values
     *
     * @see \Illuminate\Support\Str::startsWith
     */
    public function startsWith(array $values): self
    {
        return $this->addRule(new StartsWithRule($values));
    }

    /**
     * Validate that the string is a valid timezone.
     *
     * @see \DateTimeZone::listIdentifiers
     */
    public function timezone(): self
    {
        return $this->addConstraint('timezone');
    }

    /**
     * Validate that the string is a valid url.
     */
    public function url(): self
    {
        return $this->addConstraint('url');
    }

    /**
     * Validate that the string is a valid UUID
     */
    public function uuid(): self
    {
        return $this->addConstraint('uuid');
    }

    public function getType(): string
    {
        return 'string';
    }
}
