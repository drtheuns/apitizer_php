<?php

namespace Apitizer\Validation;

use Closure;

class Rules
{
    /**
     * @var (Closure|RuleBuilder)[]
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
     * Get all rules or those for a specific action method.
     *
     * @param null|string $actionMethod
     *
     * @return RuleBuilder[]|RuleBuilder
     */
    public function rules(string $actionMethod = null)
    {
        if ($actionMethod) {
            return $this->resolveRulesFor($actionMethod);
        }

        return collect($this->rules)->map(function ($rules, $actionMethod) {
            return $this->resolveRulesFor($actionMethod);
        })->all();
    }

    /**
     * Check if any rules have been defined for the given action method.
     */
    public function hasRulesFor(string $actionMethod): bool
    {
        return isset($this->rules[$actionMethod]);
    }

    /**
     * Check if any rules have been defined.
     */
    public function hasRules(): bool
    {
        return ! empty($this->rules);
    }

    /**
     * Resolve the rules for an action method from a closure to an actual list
     * of validation rules.
     *
     * Resolving is only done on an as-needed basis to prevent needless objects
     * from being created.
     */
    protected function resolveRulesFor(string $actionMethod): RuleBuilder
    {
        if (! $this->hasRulesFor($actionMethod)) {
            return new RuleBuilder;
        }

        if ($this->isAlreadyResolved($actionMethod)) {
            return $this->rules[$actionMethod];
        }

        $callback = $this->rules[$actionMethod];

        $this->rules[$actionMethod] = $this->resolveCallback($callback);

        return $this->rules[$actionMethod];
    }

    protected function resolveCallback(Closure $callback): RuleBuilder
    {
        $ruleBuilder = new RuleBuilder();

        // The callback does not return anything.
        $callback($ruleBuilder);

        return $ruleBuilder;
    }

    /**
     * Check if the rules have already been resolved for some action method.
     */
    protected function isAlreadyResolved($actionMethod)
    {
        return is_array($this->rules[$actionMethod]);
    }
}
