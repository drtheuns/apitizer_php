# Rules

The `rules` callback defines the validation rules. Rules can be defined for each
route action method (for example, `store` for a POST call). These validation
rules are then transformed into validation rules that Laravel's Validator can
use.

```php
use Apitizer\Validation\Rules;
use Apitizer\Validation\ObjectRules;

class PostSchema extends Schema
{
    // --snip-- the other callbacks

    public function rules(Rules $rules)
    {
        // Define two fields for "store" call.
        $rules->storeRules(function (ObjectRules $object) {
            $object->string('title')->required()->max(100);
            $object->string('body')->required();
        });
        
        // Same, but for the "update" call.
        $rules->updateRules(function (ObjectRules $object) {
            $object->string('title')->max(100);
            $object->string('body');
        });
        
        // Define rules for arbitrary methods
        $rules->define('my_controller_action', function (ObjectRules $object) {
            // rules...
        });
    }
}
```

Documentation is automatically generated for each rule.

## Defining rules

Defining a new validation rule always starts with the type, followed by the key
name, followed by any validation rules. Some examples:

```php
$rules->define('store', function (ObjectRules $object) {
    $object->string('name');
    $object->uuid('id'); // Alias for $object->string('id')->uuid();
    $object->boolean('accepted_terms')->accepted();
    $object->date('good_before')->before(Carbon::now()->addDays(7));
    $object->datetime('scheduled_at')->afterOrEqual(Carbon::now());
    $object->number('count')->digits(4);
    $object->integer('rating')->between(0, 5);
    $object->file('csv_import')->mimetypes(['text/csv']);
    $object->image('avatar'); // Alias for $object->file('avatar')->image();
});
```

All of the standard [Laravel validation](https://laravel.com/docs/6.x/validation#available-validation-rules)
rules have been defined on the builders. Some are only available with the
appropriate type, such as the `before` and `after` variants for dates.

### Arrays and objects

In the examples above, we really only defined scalar types. Sometimes, however,
your input will be a bit more complex with various arrays or nested objects.
These are supported by the validation rules too, and may be nested to arbitrary
depth.

```php
$rules->storeRules(function (ObjectRules $object) {
    // An array of UUIDs, with a limit of 100 elements.
    $object->array('posts')->max(100)->whereEach()->uuid();

    // An array of datetimes where each datetime must be after or equal to now.
    $object->array('available_dates')->whereEach()->datetime()->afterOrEqual(Carbon::now());
    
    // An array of objects.
    $object->array('contacts')->whereEach()->object(function (ObjectRules $object) {
        $object->string('name');
        $object->string('phonenumber');

        // objects and arrays may be nested.
        $object->array('aliases')->whereEach()->string();
    });
    
    // A single object.
    $object->object('address', function (ObjectRules $object) {
        $object->string('street');
        $object->string('city');
        // etc.
    });
});
```

## Custom rules

If you have a custom rule that you would like to add to the validation that's
not available in the builder, then you may use the `addRule` method on a field:

```php
$rules->storeRules(function (ObjectRules $object) {
    $object->string('iban')->addRule(new IbanRule);
});
```

The `addRule` method accepts three possible types:

- A string with the validation rule as you would have defined it in Laravel. For
  example, `before:tomorrow`;
- An object that implements `Illuminate\Contracts\Validation\Rule`.
- An object that implements `Apitizer\Validation\ValidationRule`.

The `Apitizer\Validation\ValidationRule` interface is primarily meant for rules
that should also be documented in the generated documentation.
