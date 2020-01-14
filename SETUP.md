# Setup

This document is meant for setting up this project locally for contributors, not
for those seeking to use the package. For information on how to set up the
project, refer to [the "Getting started" page](https://drtheuns.github.io/apitizer_php/index).

## Steps

1. Clone the repo

```
git clone git@github.com:drtheuns/apitizer_php && cd apitizer_php
```

2. Load dependencies

```
composer install
```

3. Setup testing

Create a new file, `.env`, in the project root and put your database credentials
in there:

```
DB_CONNECTION=pgsql
DB_DATABASE=apitizer_php_testing
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

You can now start testing by using the `composer test` alias, or by directly
calling `./vendor/bin/phpunit`.
