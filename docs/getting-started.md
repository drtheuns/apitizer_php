# Getting started

This page is an introduction guide to getting started with the basics of
Apitizer.

## What is Apitizer?

Apitizer is a Laravel library that primarily offers a Query Builder that allows
you to easily create documented API endpoints that are capable of filtering,
sorting, and selection of sparse fieldsets. For example, the following HTTP
request could be handled by the query builder below it.

```
/users?fields=id,name,organization(id,name)&filters[search]=John&sort=id.asc&limit=30
```

```php
<?php

namespace App\QueryBuilders;

class UserBuilder extends \Apitizer\QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'            => $this->int('id'),
            'name'          => $this->string('name'),
            'email'         => $this->string('email'),
            'organizations' => $this->association('organization', OrganizationBuilder::class),
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
        return UserBuilder::make($request)->paginate();
    }
}
```

## Install

<!-- TODO: composer require if the package ever gets published to packagist -->
Add the package repository to your `composer.json` and require the package:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://git.sr.ht/~drtheuns/apitizer_php"
    }
  ],
  "require": {
    "drtheuns/apitizer_php": "dev-master"
  }
}
```

Finally, run `composer update drtheuns/apitizer_php`.



## Setup

The setup assumes you already have your [models](https://laravel.com/docs/6.x/eloquent),
and [factories & seeders](https://laravel.com/docs/6.x/seeding) setup.

I recommend any project to always have their own base query builder, in case
they ever want to change the behaviour globally. A simple override without
anything else should be enough to get started:

```
// File: /my_project/app/QueryBuilders/QueryBuilder.php
<?php

namespace App\QueryBuilders;

abstract class QueryBuilder extends \Apitizer\QueryBuilder
{
}
```

Next, we'll add a very minimal user builder:

```php
// File: /my_project/app/QueryBuilders/UserBuilder.php
<?php

namespace App\QueryBuilders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'email' => $this->string('email'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function sorts(): array
    {
        return [];
    }

    public function model(): Model
    {
        return new User();
    }
}
```

Add a controller that uses this builder:

```
// File: /my_project/app/Http/Controllers/UserController.php
<?php

namespace App\Http\Controllers;

use App\QueryBuilders\UserBuilder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return UserBuilder::make($request)->paginate();
    }
}
```

Add the route:

```
// File: /my_projects/routes/api.php
Route::get('users', 'UserController@index');
```

Start the project with `./artisan serve`, and you can start executing requests:

```
curl localhost:8000/api/users
curl localhost:8000/api/users?fields=id
```

## Documentation

Either create a new config file: `/my_project/config/apitizer.php` or use
`./artisan vendor:publish --provider 'Apitizer\\ServiceProvider'` to publish the
configuration.

Within this config file, add (or modify) the following:

```
// File: /my_project/config/apitizer.php
return [
    'query_builders' => [
        \App\QueryBuilders\UserBuilder::class
    ],
];
```

Now when you start the webserver (`./artisan serve`) and navigate to
`localhost:8000/apidoc`, you will see the generated documentation, including the
documentation for the user builder.
