<?php

namespace Apitizer\Policies;

use Apitizer\Types\Field;

/**
 * Wrapper for other policies to cache the results.
 *
 * If a policy is expensive to run, but multiple fields share the same policy,
 * it can be expensive to have each field run that policy. To prevent that from
 * happening, the expensive policy can be wrapped in this policy which will only
 * execute it once and cache the results.
 */
class CachePolicy implements Policy
{
    /**
     * @var Policy the policy whose result should be cached.
     */
    protected $policy;

    /**
     * @var bool|null the result of the cached policy.
     */
    protected $hasPassed = null;

    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }

    public function passes($value, $row, Field $field): bool
    {
        if (is_null($this->hasPassed)) {
            $this->hasPassed = $this->policy->passes($value, $row, $field);
        }

        return $this->hasPassed;
    }
}
