<?php

namespace Apitizer\Support;

use Illuminate\Support\Str;

// Unrelated to the package, just sort of a braindump on how to improve
// controller->(service|repo) interaction by introducing a data class rather
// than using an unstructured array of data, without losing too much
// convenience.
//
// Data classes can now be introduced to form a contract between controller data
// and the repo|service:
//
// class UserData extends Data {
//     /** @var null|string */
//     public $name;
//     /** @var Organization */
//     public $organization
//     public function setOrganization($value) { $this->ensureModel(Organization::class, $value); }
// }
class Data
{
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
    }

    public function setAttribute(string $attribute, $value)
    {
        $setterMethod = Str::studly('set ' . $attribute);

        if (\method_exists($this, $setterMethod)) {
            $this->$setterMethod($value);
        } else {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    public function ensureModel(string $modelClass, $value)
    {
        $modelInstance = new $modelClass();

        if ($value instanceof $modelInstance) {
            return $value;
        }

        if (is_numeric($value)) {
            return $modelInstance->query()->find($value);
        }

        if (is_string($value) && \strlen($value) === 36) {
            // It's probably a uuid.
            return $modelInstance->query()->where('uuid', $value)->first();
        }

        throw new \UnexpectedValueException();
    }
}
