# Schema

The schema is the source of truth for what is available to clients of your API.
The fields that they are allowed to request, the filters they are allowed to
call, everything is written in a declarative way in the schema.

## Validating the schema

An Artisan command is available to validate that the schema is correctly
defined. For example, if we write an association that does not exist on the
model, we might get an error like:

```
App\Schemas\PostSchema
-----------------------------
* Association
  * Association [tags] on [App\Schemas\PostSchema] refers to association [tag] which does not exist on the model [App\Models\Post]
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

## Features of the schema

### Adding documentation metadata

The optional `apidoc` callback method may be overridden to add extra information
to the API documentation as it's created. The two most important options for
this method are to either add a description to the schema, or to add metadata
to the documentation.

```php
<?php

use Apitizer\Types\Apidoc;

class PostSchema extends Schema
{
    public function apidoc(Apidoc $apidoc): void
    {
        $apidoc->setDescription('This is a blog post resource...');
        
        // the metadata may be anything, including arrays, objects, etc.
        $apidoc->setMetadata([
            'developer' => 'John Doe'
        ]);
    }
}
```

### Building a query

The schema is able to build an Eloquent query based on the current
request:

```php
$query = UserSchema::build($request);
```

You can also build a query from a specification, rather than a request. This
specification accepts the same keys as would be present on the request without
having to actually build a request object:

```php
$query = (new UserSchema)->fromSpecification([
    'fields'  => 'id,name,posts(id,title)',
    'filters' => ['search' => 'term'],
    'sorts'   => 'id.asc'
])->buildQuery();
```

### Rendering data

If you fetch the data yourself, you can still use the schema to render
data:

```php
$user = $accountService->createUser($parameters);

return UserSchema::make($request)->render($data);
```

Just like with the query building, the rendering also works with a custom
specification.

### Fetching and rendering

There are two methods available to perform both query building and rendering in
one step. The first is the `all` method which is the same as calling `get` on
the query, and the second is the `paginate` method. A typical controller that
utilizes some of these methods might look like this:

```php
<?php

namespace App\Http\Controllers;

use App\Schemas\UserSchema;
use App\Models\User;
use Illuminate\Http\Request;

class UserController
{
    public function index(Request $request)
    {
        return UserSchema::make($request)->paginate();
    }

    public function show(Request $request, User $user)
    {
        return UserSchema::make($request)->render($user);
    }

    public function store(Request $request)
    {
        $schema = UserSchema::make($request);

        $user = (new AccountService)->createUser($request->validated());

        return $schema->render($user);
    }
}
```

The maximum number of results per page in the pagination object can be
controlled with the `maximumLimit` property on the schema, as well as the
`getMaximumLimit` and `setMaximumLimit` methods.

### Before and after query

Often times it's necessary to perform some additional logic to a query, such as
setting the right tenant, or scoping the data to something the current user is
allowed to see. The schema offers two methods to control the query a bit
more: the `beforeQuery` and `afterQuery` methods. Each of these methods accept
the current query object and the fetch specification, and return a modified
query object.

```php
<?php

use Apitizer\Types\FetchSpec;
use Illuminate\Database\Eloquent\Builder;

class UserSchema extends Schema
{
    public function beforeQuery(Builder $query, FetchSpec $fetchSpec): Builder
    {
        $user = \Auth::user();
        
        if ($user->isNotAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        return $query;
    }
}
```

Usually, you will only need to implement either the `beforeQuery` or the
`afterQuery` method.

### Validation

There are several helper methods available to get the current validation rules.
The most common helper is `validated` to get the validated data for the current
request.

```php
$schema = UserSchema::make($request);

// Get the validation rules for the current request, as can be understood by
// Laravel's Validator object.
$schema->validationRules();

// Get a Validator object with the request's input and the validation rules.
$schema->validator();

// Get the validated data. These two are the same.
$schema->validated();
$schema->validator()->validate();
```
