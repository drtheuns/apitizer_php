<?php

namespace Apitizer\Policies;

use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Illuminate\Database\Eloquent\Model;

/**
 * Policies allow a field to be visible only to those that have access to it.
 *
 * A policy is only called after the data is fetched, but before transformations
 * are applied. As a policy might depend on a specific value being present on
 * the model, the $alwaysLoadColumns property on the schema can be used
 * to ensure that the value is always available.
 */
interface Policy
{
    /**
     * Check if the value passes the validation.
     *
     * @param mixed $value the current value that is being evaluated.
     *
     * @param Model|array|mixed $row the current row that is being rendered.
     * This value will usually be a Model instance; however, the schemas
     * can be used to render just about any data. This should be taken into
     * account when writing a policy.
     *
     * @param AbstractField|Association $fieldOrAssoc the field or association
     * instance that is currently being rendered. This instance also holds a
     * reference to the current schema if that is needed in the policy.
     * Furthermore, the request instance can also be fetched from that query
     * builder.
     */
    public function passes($value, $row, $fieldOrAssoc): bool;
}
