# What is Apitizer?

Apitizer is a Laravel library that primarily offers a Schema that allows you to
easily create documented API endpoints that are capable of filtering, sorting,
and selection of sparse fieldsets. For example, the following HTTP request could
be handled by the schema below it.

```
/users?fields=id,name,organization(id,name)&filters[search]=John&sort=id.asc&limit=30

{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "organization": {
        "id": 29482,
        "name": "Big Corp 2"
      }
    }
  ],
  "first_page_url": "<url_with_query_params>",
  "from": 1,
  "last_page": 1,
  "last_page_url": "<url_with_query_params>",
  "next_page_url": null,
  "path": "<url>",
  "per_page": 30,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

```php
<?php

namespace App\Schemas;

use Apitizer\Validation\Rules;
use Illuminate\Database\Eloquent\Model;

class UserSchema extends \Apitizer\Schema
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'name'  => $this->string('name'),
            'email' => $this->string('email'),
        ];
    }
    
    public function associations(): array
    {
        return [
            'organizations' => $this->association('organization', OrganizationSchema::class),
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->filter()->search('name'),
        ];
    }

    public function sorts(): array
    {
        return [
            'id' => $this->sort()->byField('id'),
        ];
    }
    
    public function rules(Rules $rules): array
    {
    }

    public function model(): Model
    {
        return new \App\Models\User();
    }
}
```

All you have to do in the controller is:

```php
class UserController extends Controller
{
    public function index(Request $request)
    {
        return UserSchema::make($request)->paginate();
    }
}
```

## Goals

The goals of Apitizer are as follows:

- Speed up development with reusable components, such as filters and sorting.
- Improve documentation for frontend teams by generating it from the actual
  code. This way, the docs will always remain up to date.
- Make the schema inspectable to allow for tooling to be created around these schemas.
