<?php

namespace Apitizer\Policies;

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

    public function passes($value, $row, $fieldOrAssoc): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->passes($value, $row, $fieldOrAssoc)) {
                return true;
            }
        }

        return false;
    }
}
