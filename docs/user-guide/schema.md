# Schema

The schema is the source of truth for what is available to clients of your API.
The fields that they are allowed to request, the filters they are allowed to
call, everything is written in a declarative way in the schema.

## Validating the schema

An Artisan command is available to validate that the schema is correctly
defined. For example, if we write an association that does not exist on the
model, we might get an error like:

```
App\QueryBuilders\PostBuilder
-----------------------------
* Association
  * Association [tags] on [App\QueryBuilders\PostBuilder] refers to association [tag] which does not exist on the model [App\Models\Post]
```

This could easily be extended to a test case. Validating the schema in a test
case can be especially helpful to catch bugs early (for example, in CI):

```php
<?php

namespace Tests\Feature;

use Apitizer\Support\SchemaValidator;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    /** @test */
    public function it_has_a_valid_schema()
    {
        $validator = (new SchemaValidator)->validateAll();

        $this->assertFalse(
            $validator->hasErrors(),
            'Run ./artisan apitizer:validate-schema to see the errors'
        );
    }
}
```
