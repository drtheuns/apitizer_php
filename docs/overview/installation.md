# Installation

The package can be installed using composer:

```sh
composer require drtheuns/apitizer/php
```

For versions and other composer-related information, check out the [packagist
page](https://packagist.org/packages/drtheuns/apitizer_php)

## Setup

The setup assumes you already have your [models](https://laravel.com/docs/6.x/eloquent),
and [factories & seeders](https://laravel.com/docs/6.x/seeding) setup.

I recommend any project to always have their own base schema, in case
they ever want to change the behaviour globally. A simple override without
anything else should be enough to get started:

```php
// File: /my_project/app/Schemas/Schema.php
<?php

namespace App\Schemas;

abstract class Schema extends \Apitizer\Schema
{
}
```

Next, we'll add a very minimal user schema:

```php
// File: /my_project/app/Schemas/UserSchema.php
<?php

namespace App\Schemas;

use App\Models\User;
use Apitizer\Validation\Rules;
use Illuminate\Database\Eloquent\Model;

class UserSchema extends Schema
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'email' => $this->string('email'),
        ];
    }
    
    public function associations(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function sorts(): array
    {
        return [];
    }
    
    public function rules(Rules $rules)
    {
    }

    public function model(): Model
    {
        return new User();
    }
}
```

Add a controller that uses this schema:

```php
// File: /my_project/app/Http/Controllers/UserController.php
<?php

namespace App\Http\Controllers;

use App\Schemas\UserSchema;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return UserSchema::make($request)->paginate();
    }
}
```

Add the route:

```php
// File: /my_projects/routes/api.php
Route::get('users', 'UserController@index');
```

Start the project with `./artisan serve`, and you can start executing requests:

```
curl localhost:8000/api/users
curl localhost:8000/api/users?fields=id
```

## Documentation

If you have followed along with the setup, you can now generate documentation by
starting the webserver with `./artisan serve` and navigating to
`localhost:8000/apidoc`. However, if you used different namespaces, you will
need to register them in the configuration:

```
// File: /project_root/config/apitizer.php
return [
    'schemas' => [
        'classes' => [
            // Register individual classes here, for example:
            \App\Api\UserSchema::class,
        ],
        'namespaces' => [
            // Register entire namespaces here (non recursive)
            'App\Api'
        ]
    ]
];
```
