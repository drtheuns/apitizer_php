# Fields


Look at the `HasFields` trait for all available types (string, int etc). 

## Example

```
class DamageBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'            => $this->uuid('uuid'),
            'name' => $this->string('name')->nullable(),
            'created_at'      => $this->datetime('created_at'),
            'updated_at'      => $this->datetime('updated_at'),
        ];
    }
}
```

## Custom types

Extend the QueryBuilder to modify or add specific types. Here we want the string to return an empty string `""`, instead of a `null` (for backwards compatiblity)

```php
abstract class QueryBuilder extends \Apitizer\QueryBuilder
{
    protected function string(string $key): Field
    {
        return $this->field($key, 'string')->transform(static function ($value) {
            // Always cast to string, even if null, because of backwards compatibility
            return (string) $value;
        });
    }
}
```


## Callable fields 


```php
use Apitizer\Types\GeneratedField;

class InvoiceBuilder {
    public function fields(): array
    {
        return [
            'total' => $this->generatedField('int', function ($row, GeneratedField $field) {
                return 1;
            });
        ];
    }
}
```

Any `callable` is accepted, so something like this would also suffice:
```php
use Apitizer\Types\GeneratedField;

class InvoiceBuilder {
    public function fields(): array
    {
        return [
            'total' => $this->generatedField('int', new CalculateOrderTotal),
        ];
    }
}
```
