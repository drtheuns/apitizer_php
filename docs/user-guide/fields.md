# Fields

The `fields` function in the schema determines which fields are available to
clients of the API. Generally, each field has the following syntax:

```
name => type(key) [ modifiers ]
```

The `name` is the name of the field as it is visible to the client, and how it
is rendered in the resulting JSON. The `type` is used for various purposes:
casting to the right type, formatting, and generating documentation. Finally,
the `key` is the key for this piece of the data on the data source. If the data
source is an Eloquent model, then the key would be the name of the column.

## Examples

```php
public function fields(): array
{
    return [
        'id'         => $this->int('id'),
        'uuid'       => $this->uuid('uuid'),
        'title'      => $this->string('title')->description('wow'),
        'body'       => $this->string('body')
                             ->policy(new Authenticated),
        'status'     => $this->enum('status', PostStatus::all()),
        'created_at' => $this->datetime('created_at')->format(),
        'updated_at' => $this->datetime('updated_at')->format(),
    ];
}
```

Refer to `\Apitizer\Concerns\HasFields` for all the available built-in types.

## Modifiers

Most fields also accept one or more modifiers. Some are solely used for
documentation, others also affect rendering.

- `nullable()`: Determines whether this field may be null. Defaults to `false`.
  If the value from the data source (e.g. database) is `null`, but the field was
  not marked as `nullable`, then an `\Apitizer\Exceptions\InvalidOutputException`
  will be thrown. See the guide on Exception Handling for more details.
- `transform(callable)`: Adds a new transformer callable that modifies the
  output of this field when it is rendered. If some transformation happens a lot
  in your codebase, consider using an invokable class to reuse the
  transformation in many places.
- `description(string)`: Set the description to be used in the generated
  documentation.
  
The `date(time)` type also has the `format` method which formats the date(time)
to a string format. This method is implemented as just another `transform`
function.

## Defining custom types

If you followed along with the Installation guide, you should have an abstract
base schema defined for your project:

```php
<?php

namespace App\Schemas;

abstract class Schema extends \Apitizer\Schema
{
}
```

We can use this base schema to either add new types, extend the field
type, or tweak settings. We're first going to add a new `color` type that always
prefixes the color hex code with a pound sign:

```
<?php

namespace App\Schemas;

use Apitizer\Types\Field;

abstract class Schema extends \Apitizer\Schema
{
    public function color(string $key): Field
    {
        return $this->string($key)->transform(static function ($hexvalue) {
            return "#$hexvalue";
        });
    }
}
```

Optionally, you could also abstract the color out to it's own class that extends
from Field, and return an instance of this class. This is also how, for example,
enums as implemented as a separate type. See `\Apitizer\Types\EnumField` for an
example.

## Generated fields

Apitizer supports generated fields. This takes a type and a callable. The
callable accepts up to two parameters: the row of data that is currently being
rendered, and the `\Apitizer\Types\GeneratedField` instance.

```php
class InvoiceSchema {
    public function fields(): array
    {
        return [
            'total' => $this->generatedField('int', function ($row) {
                return 1;
            });
        ];
    }
}
```

Any `callable` is accepted, so something like this could also be used:

```php
use Apitizer\Types\GeneratedField;

class InvoiceSchema {
    public function fields(): array
    {
        return [
            'total' => $this->generatedField('int', new CalculateOrderTotal),
        ];
    }
}
```

```php
class CalculateOrderTotal {
    public function __invoke($row) {
        //
    }
}
```
