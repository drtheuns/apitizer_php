// <?php

// namespace Apitizer\Validation;

// use Apitizer\Validation\Rules\ExistsRule;
// use Apitizer\Validation\Rules\RequiredRule;
// use Apitizer\Validation\Rules\UniqueRule;
// use Apitizer\Validation\Rules\InRule;
// use Apitizer\Validation\Rules\NotInRule;
// use Apitizer\Validation\Rules\SimpleRule;
// use Illuminate\Contracts\Validation\Rule;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Arr;

// abstract class TypedRuleBuilderTemp
// {
//     /**
//      * @var (ValidationRule|Rule|string)[] Holds the various rules that have
//      * been added to the current field.
//      */
//     protected $rules = [];

//     /**
//      * @var string
//      */
//     protected $name;

//     /**
//      * @var ValidationRule|null
//      */
//     protected $required;

//     /**
//      * @var bool
//      */
//     protected $nullable = false;

//     /**
//      * @var null|array
//      */
//     protected $rawRules;

//     /**
//      * Get the type information of the current field.
//      */
//     abstract public function getType(): string;

//     /**
//      * @param string $name the name of the field.
//      */
//     public function __construct(string $name)
//     {
//         $this->name = $name;
//     }

//     /**
//      * Instruct the validator to stop validating any rules that come after this
//      * rule.
//      */
//     public function bail(): self
//     {
//         // Doesn't need to show up in the documentation.
//         return $this->addRule('bail');
//     }

//     /**
//      * There must be another field by the same name suffixed "_confirmation"
//      * that has the same value.
//      */
//     public function confirmed(): self
//     {
//         return $this->addSimpleRule(
//             'confirmed', null,
//             $this->trans('confirmed', ['field' => "{$this->name}_confirmation"])
//         );
//     }

//     /**
//      * @param int|float $min
//      * @param int|float $max
//      */
//     public function between($min, $max): self
//     {
//         return $this->addSimpleRule(
//             'between', [$min, $max],
//             $this->trans('between', ['min' => $min, 'max' => $max])
//         );
//     }

//     /**
//      * Validate that this field is different than the given $field.
//      *
//      * @param string $field
//      */
//     public function different(string $field): self
//     {
//         return $this->addSimpleRule(
//             'different', [$field],
//             $this->trans('different', ['field' => $field])
//         );
//     }

//     /**
//      * Validate that this field is the same as the given $field.
//      *
//      * @param string $field
//      */
//     public function same(string $field): self
//     {
//         return $this->addSimpleRule(
//             'same', [$field],
//             $this->trans('same', ['field' => $field])
//         );
//     }

//     public function regex(string $regex): self
//     {
//         $regex = \ltrim($regex, '/');

//         return $this->addSimpleRule(
//             'regex', ["/$regex/"],
//             $this->trans('regex', ['regex' => $regex])
//         );
//     }

//     public function notRegex(string $regex): self
//     {
//         $regex = \ltrim($regex, '/');

//         return $this->addSimpleRule(
//             'not_regex', ["/$regex/"],
//             $this->trans('not_regex', ['regex' => $regex])
//         );
//     }

//     /**
//      * @param int|float max
//      */
//     public function max($max): self
//     {
//         return $this->addSimpleRule(
//             'max', [$max],
//             $this->trans('max', ['max' => $this->sizeSuffix($max)])
//         );
//     }

//     /**
//      * @param int|float $min
//      */
//     public function min($min): self
//     {
//         return $this->addSimpleRule(
//             'min', [$min],
//             $this->trans('min', ['min' => $min])
//         );
//     }

//     /**
//      * @param int|float $greaterThan
//      */
//     public function gt($field): self
//     {
//         return $this->addSimpleRule(
//             'gt', [$field],
//             $this->trans('gt', ['field' => $field])
//         );
//     }

//     public function gte($field): self
//     {
//         return $this->addSimpleRule(
//             'gte', [$field],
//             $this->trans('gte', ['field' => $field])
//         );
//     }

//     /**
//      * @param int|float $field
//      */
//     public function lt($field): self
//     {
//         return $this->addSimpleRule(
//             'lt', [$field],
//             $this->trans('lt', ['field' => $field])
//         );
//     }

//     /**
//      * @param int|float $field
//      */
//     public function lte($field): self
//     {
//         return $this->addSimpleRule(
//             'lte', [$field],
//             $this->trans('lte', ['field' => $field])
//         );
//     }

//     public function in(array $values): self
//     {
//         return $this->addRule(new InRule($values));
//     }

//     public function notIn(array $values): self
//     {
//         return $this->addRule(new NotInRule($values));
//     }

//     public function inArray(string $field): self
//     {
//         return $this->addSimpleRule(
//             'in_array', [rtrim($field, '.*') . '.*'],
//             $this->trans('in_array', ['field' => $field])
//         );
//     }

//     /**
//      * @param int|float $size
//      */
//     public function size($size): self
//     {
//         return $this->addSimpleRule(
//             'size', [$size],
//             $this->trans('size', ['size' => $size])
//         );
//     }

//     /**
//      * @param string $table either the name of the table or the model class.
//      * @param string $column the name of the column to check.
//      *
//      * @see https://laravel.com/docs/6.x/validation#rule-exists
//      */
//     public function exists(string $table, string $column = null)
//     {
//         return $this->addRule(new ExistsRule($table, $column));
//     }

//     /**
//      * @param string $table either the name of the table or the model class.
//      * @param string $idColumn the name of the column to compare.
//      *
//      * @see https://laravel.com/docs/6.x/validation#rule-unique
//      */
//     public function unique(string $table, string $idColumn = null)
//     {
//         return $this->addRule(new UniqueRule($table, $idColumn));
//     }

//     /**
//      * The value may not be empty if this field is passed in.
//      */
//     public function filled()
//     {
//         return $this->addSimpleRule('filled');
//     }

//     public function sometimes(): self
//     {
//         return $this->addSimpleRule('sometimes');
//     }

//     public function present(): self
//     {
//         return $this->addSimpleRule('present');
//     }

//     public function required(): self
//     {
//         return $this->addRequiredRule('required');
//     }

//     public function requiredIf(RequiredIfRule $rule): self
//     {
//         return $this->addRule($field);
//     }

//     public function requiredUnless(string $field, $value): self
//     {
//         // TODO: $values should allow array
//         return $this->addRequiredRule(
//             'required_unless', [$field, $value],
//             $this->trans('required_unless', ['field' => $field, 'value' => (string) $value])
//         );
//     }

//     /**
//      * The field is required and not empty only if any of the other specified
//      * fields are present.
//      */
//     public function requiredWith(array $fields): self
//     {
//         return $this->addRequiredRule(
//             'required_with', $fields,
//             $this->trans('required_with', ['fields' => implode(' or ', $fields)])
//         );
//     }

//     public function requiredWithAll(array $fields): self
//     {
//         return $this->addRequiredRule(
//             'required_with_all', $fields,
//             $this->trans('required_with_all', ['fields' => implode(' and ', $fields)])
//         );
//     }

//     public function requiredWithout(array $fields): self
//     {
//         return $this->addRequiredRule(
//             'required_without', $fields,
//             $this->trans('required_without', ['fields' => implode(' or ', $fields)])
//         );
//     }

//     public function requiredWithoutAll(array $fields): self
//     {
//         return $this->addRequiredRule(
//             'required_without_all', $fields,
//             $this->trans('required_without_all', ['fields' => implode(' and ', $fields)])
//         );
//     }

//     public function nullable(bool $nullable = true): self
//     {
//         $this->nullable = $nullable;

//         return $this->addSimpleRule('nullable');
//     }

//     /**
//      * @internal
//      */
//     protected function digitsRule(int $length): self
//     {
//         return $this->addSimpleRule(
//             'digits', [$length],
//             $this->trans('digits', ['length' => $length])
//         );
//     }

//     /**
//      * @internal
//      */
//     protected function digitsBetweenRule(int $min, int $max): self
//     {
//         return $this->addSimpleRule(
//             'digits_between', [$min, $max],
//             $this->trans('digits_between', ['min' => $min, 'max' => $max])
//         );
//     }

//     /**
//      * @internal
//      */
//     protected function endsWithRule($values)
//     {
//         $values = Arr::wrap($values);

//         return $this->addSimpleRule(
//             'ends_with', $values,
//             $this->trans('ends_with', ['values' => implode(' or ', $values)])
//         );
//     }

//     /**
//      * @internal
//      */
//     protected function startsWithRule($values)
//     {
//         $values = Arr::wrap($values);

//         return $this->addSimpleRule(
//             'starts_with', $values,
//             $this->trans('starts_with', ['values' => implode(' or ', $values)])
//         );
//     }

//     /**
//      * Override the validation rules that should be generated by this field.
//      *
//      * Expects an array with key=>rules pairs as would be passed to the validator.
//      *
//      * @param array<string, string|array> $rules
//      */
//     protected function rawRules(array $rules): self
//     {
//         $this->rawRules = $rules;

//         return $this;
//     }

//     /**
//      * Add a new rule to the field.
//      *
//      * @param ValidationRule|\Illuminate\Contracts\Validation\Rule|string $rule
//      */
//     public function addRule($rule): self
//     {
//         $this->rules[] = $rule;

//         return $this;
//     }

//     /**
//      * Add a simple rule without arguments.
//      */
//     protected function addSimpleRule(
//         $rule,
//         array $parameters = null,
//         ?string $description = null
//     ) {
//         return $this->addRule(new SimpleRule($rule, $parameters, $description));
//     }

//     protected function addRequiredRule(
//         $rule,
//         array $parameters = null,
//         ?string $description = null
//     ) {
//         $this->required = new SimpleRule($rule, $parameters, $description);

//         return $this;
//     }

//     /**
//      * @internal
//      */
//     public function getName(): string
//     {
//         return $this->name;
//     }

//     /**
//      * @internal
//      */
//     public function isRequired(): bool
//     {
//         return (bool) $this->required;
//     }

//     /**
//      * @internal
//      */
//     public function isNullable(): bool
//     {
//         return (bool) $this->nullable;
//     }

//     /**
//      * @internal
//      */
//     public function addValidationRulesTo(Collection $rules): Collection
//     {
//         if ($this->rawRules) {
//             return $rules->merge($this->rawRules);
//         }

//         $validationRules = [$this->getTypeRule()];

//         if ($this->required) {
//             $validationRules[] = $this->required->toValidationRule();
//         }

//         foreach ($this->rules as $rule) {
//             if ($rule instanceof ValidationRule) {
//                 $validationRules[] = $rule->toValidationRule();
//                 continue;
//             }

//             if ($rule instanceof Rule || is_string($rule)) {
//                 $validationRules[] = $rule;
//             }
//         }

//         return $rules->put($this->getName(), $validationRules);
//     }

//     public function addDocumentationTo(Collection $collection): Collection
//     {
//         return $collection->push($this);
//     }

//     /**
//      * @internal
//      */
//     public function rulesToDocumentation(): array
//     {
//         $documentationLines = [];

//         foreach ($this->rules as $rule) {
//             if ($rule instanceof DocumentableRule) {
//                 $documentationLines[] = $rule->getDescription();
//             }
//         }

//         return $documentationLines;
//     }

//     public function hasRules(): bool
//     {
//         return collect($this->rules)->filter(function ($rule) {
//             return $rule instanceof DocumentableRule;
//         })->isNotEmpty();
//     }

//     protected function getTypeRule()
//     {
//         return $this->getType();
//     }

//     protected function trans(string $name, $replace = [], $locale = null)
//     {
//         return trans("apitizer::validation.$name", $replace, $locale);
//     }
// }
