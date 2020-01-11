<?php

namespace Apitizer\Policies;

use Apitizer\Types\Concerns\FetchesValueFromRow;
use Apitizer\Types\Field;

/**
 * This policy can be used to check if the current object is owned by the
 * current user through a column on the object.
 *
 * For example, if there are "posts" and "users" and a post belongs to a user
 * through an 'author_id' column, this policy will be able to check if the post
 * belongs to the currently logged in user.
 *
 * In simpler terms, this policy can compare a field on the user to a field on
 * the current object. By default it compares against the logged-in user's
 * primary key.
 */
class OwnerPolicy implements Policy
{
    use FetchesValueFromRow;

    /**
     * @var string the key on the current field to compare.
     */
    protected $localKey;

    /**
     * @var string|null the key on the user model to use in the comparison.
     */
    protected $userKey;

    public function __construct(string $localKey, string $userKey = null)
    {
        $this->localKey = $localKey;
        $this->userKey = $userKey;
    }

    public function passes($value, $row, $fieldOrAssoc): bool
    {
        $user = $fieldOrAssoc->getQueryBuilder()->getRequest()->user();

        if (! $user) {
            return false;
        }

        $userValue = $this->userKey ? $user[$this->userKey] : $user->getKey();

        return $this->valueFromRow($row, $this->localKey) === $userValue;
    }
}
