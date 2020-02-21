<?php

namespace Apitizer\Validation;

class StringRuleBuilder extends TypedRuleBuilder
{
    /**
     * Validate that the string is a url according contains a valid A or AAAA
     * record according to `dns_get_record`.
     */
    public function activeUrl(): self
    {
        return $this->addSimpleRule('active_url');
    }

    /**
     * Validate that the string consists only of alphabetic characters.
     */
    public function alpha(): self
    {
        return $this->addSimpleRule('alpha');
    }

    /**
     * Validate that the string consists only of alphanumeric characters,
     * dashes, and underscores.
     */
    public function alphaDash(): self
    {
        return $this->addSimpleRule('alpha_dash');
    }

    /**
     * Validate that the string consists only of alphanumeric characters.
     */
    public function alphaNum(): self
    {
        return $this->addSimpleRule('alpha_num');
    }

    /**
     * Validate that the string is numeric consisting of exactly $length digits.
     */
    public function digits(int $length): self
    {
        return $this->digitsRule($length);
    }

    /**
     * Validate that the string is numeric consisting between $min and $max digits.
     */
    public function digitsBetween(int $min, int $max): self
    {
        return $this->digitsBetweenRule($min, $max);
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
        return $this->addSimpleRule(
            'email', $validationStyle,
            $this->trans('email')
        );
    }

    /**
     * Validate that the string ends with one of the given values.
     *
     * @param array|string $values
     *
     * @see \Illuminate\Support\Str::endsWith
     */
    public function endsWith($values): self
    {
        return $this->endsWithRule($values);
    }

    /**
     * Validate that the string is a valid IP address.
     */
    public function ip(): self
    {
        return $this->addSimpleRule('ip');
    }

    /**
     * Validate that the string is a valid IPv4 address.
     */
    public function ipv4(): self
    {
        return $this->addSimpleRule('ipv4');
    }

    /**
     * Validate that the string is a valid IPv6 address.
     */
    public function ipv6(): self
    {
        return $this->addSimpleRule('ipv6');
    }

    /**
     * Validate that the string is a valid json string.
     */
    public function json(): self
    {
        return $this->addSimpleRule('json');
    }

    /**
     * Validate that the string is numeric.
     */
    public function numeric(): self
    {
        return $this->addSimpleRule('numeric');
    }

    /**
     * Validate that the string starts with one of the given values.
     *
     * @param array|string $values
     *
     * @see \Illuminate\Support\Str::startsWith
     */
    public function startsWith($values): self
    {
        return $this->startsWithRule($values);
    }

    /**
     * Validate that the string is a valid timezone.
     *
     * @see \DateTimeZone::listIdentifiers
     */
    public function timezone(): self
    {
        return $this->addSimpleRule('timezone');
    }

    /**
     * Validate that the string is a valid url.
     */
    public function url(): self
    {
        return $this->addSimpleRule('url');
    }

    /**
     * Validate that the string is a valid UUID
     */
    public function uuid(): self
    {
        return $this->addSimpleRule('uuid');
    }

    /**
     * @internal
     */
    public function getType(): string
    {
        return 'string';
    }
}
