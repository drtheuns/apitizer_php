<?php

namespace Apitizer\Policies;

use Apitizer\Types\Field;

/**
 * Passes if any of the given policies pass.
 */
class AnyPolicy implements Policy
{
    /**
     * @var Policy[]
     */
    protected $policies = [];

    public function __construct(Policy ...$policies)
    {
        $this->policies = $policies;
    }

    public function passes($value, $row, Field $field): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->passes($value, $row, $field)) {
                return true;
            }
        }

        return false;
    }
}
