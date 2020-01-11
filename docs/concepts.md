# Concepts and goals

This document enumerates the different design goals and concepts of this package.

## Schema

Eloquent does not define a schema in it's models, so the query builder takes on
that role. This allows for very specific selection of columns from the database
instead of the typical `select *`.

The schema can be validated using `artisan apitizer:validate-schema`. This will
print a list of errors that it managed to find with your query builders. An
example of the about can be seen below:

```
QueryBuilders\PostBuilder
-----------------------------
* Association
  * Association [tags] on [App\QueryBuilders\PostBuilder] refers to association [tag] which does not exist on the model [App\Models\Post]
```


## Fields

Fields allow consumers of the API to select only the columns that they need.
These field selects were based on
[JSON-API](https://jsonapi.org/format/#fetching-sparse-fieldsets) and
[PostgREST](https://postgrest.org/en/v6.0/api.html#vertical-filtering-columns).

The ability for clients to specify which fields should be fetched adds some
constraints to the backend. Namely, each field should be treated independently
because it is known ahead of time which fields will be selected. Consequently,
things such as transformations, validation, rendering, etc. should be done
individually.
