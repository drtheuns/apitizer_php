<?php

namespace Apitizer\Validation;

use Illuminate\Contracts\Validation\Rule;
use Closure;

class Rules
{
    /**
     * @var (Closure|ObjectRules)[]
     */
    protected $rules;

    /**
     * Define new validation rules for some route action.
     *
     * The callback is passed a RuleBuilder instance that can be used to define
     * validation rules on.
     *
     * @param string $routeAction
     * @param Closure $callback
     *
     * @return self
     */
    public function define(string $routeAction, Closure $callback): self
    {
        $this->rules[$routeAction] = $callback;

        return $this;
    }

    /**
     * Shorthand for defining rules for the store method.
     *
     * @see Rules::define
     */
    public function storeRules(Closure $callback): self
    {
        return $this->define('store', $callback);
    }

    /**
     * Shorthand for defining rules for the update method.
     *
     * @see Rules::define
     */
    public function updateRules(Closure $callback): self
    {
        return $this->define('update', $callback);
    }

    /**
     * Get all the validation rule builders.
     *
     * @return array<string, ObjectRules>
     */
    public function getBuilders(): array
    {
        return collect($this->rules)->map(function ($rules, string $actionMethod) {
            return $this->resolveRulesFor($actionMethod);
        })->all();
    }

    /**
     * Get the validation rule builder for a specific action method.
     *
     * @return ObjectRules
     */
    public function getBuilder(string $actionMethod): ObjectRules
    {
        return $this->resolveRulesFor($actionMethod);
    }

    /**
     * Get the validation rules for all the action methods.
     *
     * @return array<string, array<string, string|Rule>>
     */
    public function getValidationRules(): array
    {
        return collect($this->getBuilders())->map(function (ObjectRules $builder) {
            return RuleInterpreter::rulesFrom($builder);
        })->all();
    }

    /**
     * Get the validation rules for a specific action.
     *
     * @return array<string, string|Rule>
     */
    public function getValidationRulesForAction(string $actionMethod): array
    {
        return RuleInterpreter::rulesFrom($this->getBuilder($actionMethod));
    }

    /**
     * Check if any rules have been defined.
     */
    public function hasRules(): bool
    {
        return !empty($this->rules);
    }

    /**
     * Check if any rules have been defined for the given action method.
     */
    public function hasRulesFor(string $actionMethod): bool
    {
        return isset($this->rules[$actionMethod]);
    }

    /**
     * Resolve the rules for an action method from a closure to an actual list
     * of validation rules.
     *
     * Resolving is only done on an as-needed basis to prevent needless objects
     * from being created.
     */
    protected function resolveRulesFor(string $actionMethod): ObjectRules
    {
        if (! $this->hasRulesFor($actionMethod)) {
            return new ObjectRules(null, function () {
            });
        }

        $object = $this->rules[$actionMethod];

        if ($object instanceof ObjectRules) {
            return $object;
        }

        // Resolve the closure and cache the results.
        $object = (new ObjectRules(null, $object));
        $object->resolve();
        $this->rules[$actionMethod] = $object;

        return $object;
    }
}
