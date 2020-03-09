# Policies

Policies are essentially callbacks for fields that determine if the user is
allowed to view that field based on the loaded data. For example, for a public
API, you will probably want to hide certain data from unauthenticated requests,
while other data might be visible. This page will go over creating such a
policy. It will assume you have already read the Getting Started page.

## Defining the builders

We'll start with a single resource: users. A user usually has a bunch of
sensitive data that should not be visible to just about anyone, such as their
address, birth date, email, etc.

```php
// File: /my_project/app/QueryBuilders/UserBuilder.php
<?php

namespace App\QueryBuilders;

class UserBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'           => $this->int('id'),
            'name'         => $this->string('name'),
            'email'        => $this->string('email'),
            'birthdate'    => $this->date('birthdate'),
            'member_since' => $this->datetime('member_since'),
        ];
    }
    
    // filters(), sorts(), and model() omitted for brevity.
}
```

With the above query builder, anyone can access any of the fields.

## Writing a policy

To manage the visibility of certain data, we'll write a policy that defines that
the request must come from an authenticated request; that is, there is a logged
in user available on the `Request` object.

```php
// File: /my_project/app/QueryBuilders/Policies/Authenticated.php
<?php

namespace App\QueryBuilder\Policies;

use Apitizer\Policies\Policy;

class Authenticated implements Policy
{
    public function passes($value, $row, $fieldOrAssociation): bool
    {
        return !! $fieldOrAssociation->getQueryBuilder()->getRequest()->user();
    }
}
```

The `Authenticated` policy above fetches the request that is currently being
handled, and checks that there is a user on that request. We could have used the
`request()` global helper that Laravel defines, but this makes it harder to test
if we're using custom `Request` objects during testing.

Now that we have a policy, we can apply it to a field. In this case we'll state
that the `email`, `birthdate`, and `member_since` fields require the user to be
logged in.

```php
// File: /my_project/app/QueryBuilders/UserBuilder.php
<?php

namespace App\QueryBuilders;

use App\QueryBuilders\Policies\Authenticated;

class UserBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'           => $this->int('id'),
            'name'         => $this->string('name'),
            'email'        => $this->string('email')
                                   ->policy(new Authenticated),
            'birthdate'    => $this->date('birthdate')
                                   ->policy(new Authenciated),
            'member_since' => $this->datetime('member_since')
                                   ->policy(new Authenticated),
        ];
    }

    // filters(), sorts(), and model() omitted for brevity.
}
```

If you were to now issue an unauthenticated request with the following
selection: `?fields=id,name,email`, you would only get back the `id` and the
`name`, the `email` field will be gone.

## Multiple policies

A field may have more than one policy defined on it. The `policy` method accepts
multiple `Policy` objects to be given. For example:

```php
public function fields(): array
{
    return [
        'email' => $this->string('email')
                        ->policy(new Authenticated, new IsViewingThemselves)
    ];
}
```

In this scenario, the `Authenticated` and `IsViewingThemselves` policies must
both pass before the field as a whole passes. However, you might want to check
multiple policies for at least one that passes. For example, you might have an
`IsAdmin` policy that, if it passes, should ignore all the other policies. For
these use cases, there is the `policyAny` method that accepts a list of policies
and checks, in order, for the first policy that passes. If none pass, the policy
check fails:

```php
public function fields(): array
{
    return [
        'email' => $this->string('email')
                        ->policyAny(new IsAdmin, new IsViewingThemselves)
    ];
}
```

The `policy` and `policyAny` function can also be chained.

## Policies on associations

Policies on associations function exactly the same as policies on fields: the
policy will receive all the data from that association. If the policy fails, the
association will not be rendered.

The data the policy receives will be the same as directly accessing the relation
on the model:

```
$user->posts   // hasMany
$post->author  // belongsTo
```

No distinction is made based on the number of rows that an association returns.

## Caching expensive policies

As you might have already noticed, defining new policies for each field might be
slow if the policy itself is an expensive operation. For example, if the
`Authenticated` policy from the "Writing a policy" section used multiple
database calls it would take unnecessarily long if the policy was called for all
fields that use that policy. In this case, the policy itself does not use the
field value (first parameter to the `passes` method in a Policy) at all and can
be applied to any field. That also means that whichever field uses the policy
will get the same result. The `Authenticated` policy can therefore be easily
cached. Luckily, `Apitizer` already defines a helper for this: `CachedPolicy`:

```php
// File: /my_project/app/QueryBuilders/UserBuilder.php
<?php

namespace App\QueryBuilders;

use App\QueryBuilders\Policies\Authenticated;
use Apitizer\Policies\CachedPolicy;

class UserBuilder extends QueryBuilder
{
    public function fields(): array
    {
        $policy = new CachedPolicy(new Authenticated);

        return [
            'id'           => $this->int('id'),
            'name'         => $this->string('name'),
            'email'        => $this->string('email')->policy($policy),
            'birthdate'    => $this->date('birthdate')->policy($policy),
            'member_since' => $this->datetime('member_since')->policy($policy),
        ];
    }

    // filters(), sorts(), and model() omitted for brevity.
}
```

With our new cached policy in place, the `Authenticated` policy will only be
called once, regardless of how many fields use the policy.

## Ensuring data is fetched

Some policies might be dependent on certain data being available in order to
pass. For example, a policy that checks if the row of data belongs to a user
using a `user_id` column, is dependent on this column being present. However, if
the client never requested this column, or if the column is not available to the
user to begin with, the policy would never pass. To solve this, the query
builder has an `alwaysLoadColumns` property:

```php
class PostBuilder {
    protected $alwaysLoadColumns = ['author_id'];
}
```

This ensures that the `author_id` is always loaded by the query builder, making
it available to use in policies.
