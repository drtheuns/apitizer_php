# Exception handling

There are several things that can go wrong while Apitizer handles a request. By
default, exceptions are raised. This can be tweaked in several ways. Generally,
there are 3 ways to change the exception handling:

- Globally
- For one or multiple query builders.
- For one specific query builder instance.

All of these cases are accomplished in the same way: by changing the
ExceptionStrategy. There are two strategies available out of the box:

- `Apitizer\ExceptionStrategy\Raise` raise the exception. This is the default behaviour.
- `Apitizer\ExceptionStrategy\Ignore` ignore the exception.

## Global exception handler

To change the default exception strategy for all query builders, you can bind
your preferred strategy in Laravel's container. This can be done in [a
provider](https://laravel.com/docs/6.x/providers).

```
<?php

use Apitizer\ExceptionStrategy\Ignore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Set the default to ignore.
        $this->app->bind(Strategy::class, Ignore::class);
    }
}
```

## One or multiple query builders

Every `Apitizer\QueryBuilder` has the `getExceptionStrategy` method that returns
an instance of the Strategy class. You can override this method with your own
implementation that returns the exception strategy for that specific query
builder (or any of it's children, if you declare it on an abstract base class).

```php
<?php

use Apitizer\ExceptionStrategy\Strategy;

class PostBuilder extends QueryBuilder
{
    public function getExceptionStrategy(): Strategy
    {
        return MyStrategyImplementation();
    }
}
```

## Per instance

Besides the `getExceptionStrategy` method, there's also the
`setExceptionStrategy` that can be used on a per-instance basis to set the
appropriate strategy. This method accepts either `null` (to reset the current
exception strategy back to its default), or a `Strategy` implementation.

## Custom strategies

A custom strategy can be useful to tweak the behaviour of Apitizer. Furthermore,
certain exceptions only occur when a programmer error has caused it (for
example, by writing an invalid schema) and you will likely want to log these
messages to some service or file.

```php
<?php

use Apitizer\ExceptionStrategy\Strategy;
use Apitizer\QueryBuilder;
use Apitizer\Exceptions\ApitizerException;
use Apitizer\Exceptions\InvalidOutputException;

class MyStrategy implements Strategy
{
    public function handle(QueryBuilder $queryBuilder, ApitizerException $exception): void
    {
        if ($exception instanceof InvalidOutputException) {
            // log to sentry, bugsnag, or w/e
        }
        
        throw $exception;
    }
}
```

## Recommended

The `Apitizer\Exceptions\InvalidOutputException` is an important exception to
handle in your strategy, as this exception only occurs when there is a mismatch
between the schema definition and the data that was actually fetched from the
data source. When this exception reaches your strategy, it should ideally alert
one of the developers that there is a mistake in the schema definition that
should be fixed ASAP. The exception itself contains a wealth of information on
where this error occurred and how it might be fixed.

With the combination of the `./artisan apitizer:validate-schema` command, and
the possibility of running the schema validator in a test, you should strife to
never see `Apitizer\Exceptions\DefinitionException` in production. If it does
occur, then same as the `InvalidOutputException` it should alert a developer and
it should be fixed ASAP.
