# Filters

Filters are essentially callbacks that consumers of your API can invoke by
passing in the correct query parameters. These callbacks mutate the query that
is constructed when fetching the data for the response.

## Input types

Each filter defines a type that they expect as input and the number of
parameters they expect. By default, a filter will expect a single string value.
The `expect` and `expectMany` methods may be used to alter these expectations.
For example:

```php
public function filters(): array
{
    // These are not valid filters, as they don't define a handler, 
    // but we'll get to that in the next section.
    return [
        // Expect a single string value. This is the default.
        'name' => $this->filter()->expect()->string(),

        // Expect a single date.
        'created_after' => $this->filter()->expect()->date(),

        // Expect a single date with a custom format.
        'created_before' => $this->filter()->expect()->date('d-m-Y'),

        // Expects an array of UUIDs
        'groups' => $this->filter()->expectMany()->uuid(),

        // Expect a single boolean value
        'is_active' => $this->filter()->expect()->boolean(),
    ];
}
```

If the given input does not match the expected types, an `InvalidInputException`
will the thrown. This is to prevent the consumers of the API from receiving a
response where they think the filter has been applied, but instead it might have
been silently ignored.

## Defining callbacks

Besides expected input type, the filter must also specify a callback. This
callback is the function that actually applies the filter, and can be set using
the `handleUsing` method. A callback expects two parameters:

- The `\Illuminate\Database\Eloquent\Builder` instance that is currently being
  built.
- The input from the consumer of the API from the query parameters.

The input is automatically cast to the correct type as specified in the previous
section.

```php
public function filters(): array
{
    return [
        // Handle the filter with a Closure.
        'name' => $this->filter()->handleUsing(function (Builder $query, string $value) {
            $query->where('name', 'like', "$value%");
        }),

        // Handle the filter with an invokable class
        'name' => $this->filter()->handleUsing(new LikeFilter('name')),
    ];
}
```

The example above highlights a filter using an invokable class. The
implementation of this class could look like:

```php
class LikeFilter
{
    protected $fields;

    public function __construct($fields)
    {
        $this->fields = is_array($fields) ? $fields : func_get_args();
    }

    public function __invoke(Builder $query, string $value)
    {
        $searchTerm = '%' . $value . '%';

        $query->where(function ($query) use ($searchTerm) {
            foreach ($this->fields as $field) {
                $query->orWhere($field, 'like', $searchTerm);
            }
        });
    }
}
```

In fact, this is exactly how the `search` filter from the next section is implemented.

## Built-in filters

There are a few built-in filter handlers. These are `search`, `byField` and
`byAssociation`.

### `search`

The `search` filter is implemented as a simple `LIKE` query over one or several
fields. It uses the `LikeFilter` class from the previous section.

```php
public function filters(): array
{
    return [
        'search' => $this->filter()->search(['first_name', 'last_name'])->expect()->string(),
    ];
}
```

### `byField`

This is a very simple filter that simply compares the given value against a
single column in the database. This is primarily useful for "comparison"-type
filters. It's the same as adding a `where` or `whereIn` clause to the query.

The first argument is the name of the column, while the optional second column
is the comparison operator.

```php
public function filters(): array
{
    return [
        'published_after' => $this->filter()->expect()->date()->byField('published', '>'),
        'statuses' => $this->filter()->expectMany()->string()->byField('status'),
    ];
}
```

### `byAssociation`

Filtering by association allows you to answer API calls such as: "get all users
that are part of organization X":

```
/users?filters[organization]=5bd9aaba-0928-4c01-93c2-b438beca934d
```

```php
public function filters(): array
{
    return [
        'organization' => $this->filter()->byAssociation('organization', 'uuid')
    ];
}
```

The first argument is the name of the relation as defined on the Eloquent model.
The second, optional parameters defines the field that should be checked
against. In the example above, the `uuid` field on the `organizations` table is
compared against the given UUID. The key will default to the primary key on the
related model.

## Documentation

The `description` method can be used to add documentation to a filter that will
be displayed in the generated documentation page:

```php
public function filters(): array
{
    return [
        'name' => $this->filter()
                       ->search('name')
                       ->description('Search the name field for the given input')->expect()->string();
    ];
}
```
