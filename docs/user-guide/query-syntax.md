# Query Syntax

The interface that is exposed by the schemas to the API clients works primarily
with query parameters. This guide explains this query syntax for each of the
parameters.

All of the syntax examples below are based on the schema below. Don't worry
about some weird design decisions (an `id` and a `uuid`?); it's just an example.

```php
<?php

namespace App\QueryBuilders;

use Apitizer\Validation\Rules;
use Illuminate\Database\Eloquent\Model;

class UserBuilder extends \Apitizer\QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'         => $this->int('id'),
            'uuid'       => $this->uuid('uuid'),
            'title'      => $this->string('title')->description('wow'),
            'body'       => $this->string('body'),
            'status'     => $this->enum('status', PostStatus::all()),
            'created_at' => $this->datetime('created_at')->format(),
            'updated_at' => $this->datetime('updated_at')->format(),
        ];
    }
    
    public function associations(): array
    {
        return [
            'author'   => $this->association('author', UserBuilder::class),
            'comments' => $this->association('comments', CommentBuilder::class)
                                 ->description('People always have an opinion.'),
            'tags'     => $this->association('tags', TagBuilder::class),
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->filter()->search('name'),
            'status' => $this->filter()->expect()->array()
                             ->whereEach()->enum(PostStatus::all())->byField('status'),
        ];
    }

    public function sorts(): array
    {
        return [
            'id'   => $this->sort()->byField('id'),
            'name' => $this->sort()->byField('name'),
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

## Fields and associations

Sparse fieldsets is a concept that should be familiar to anyone that has ever
worked with GraphQL or JSON-API. The syntax for Apitizer's `fields` parameter is
based on [PostgREST](http://postgrest.org/en/v6.0/api.html#vertical-filtering-columns).

To select only specific fields:

```
?fields=id,uuid,title,status
```

Associations can also be selected, including nested associations:

```
?fields=id,title,author(id,name),comments(id,body,author(id,name))
```

Whitespace between fields is allowed and will be automatically trimmed. The
following two are equal:

```
?fields=id, title,   author(id,name)
?fields=id,title,author(id,name)
```

Expressions may be quoted using `"`:

```
?fields=id,"  preserve spaces  "
```

Quoted expressions may also contain "reserved" letters. So the following
expression will be considered as two fields: `id` and `author(id,name)`. It will
**not** interpret the author as an association.

```
?fields=id,"author(id,name)"
```

Fields may also be passed as a list, but you lose the ability to select
associations:

```
?fields[]=id&fields[]=title&fields[]=status
```

## Filters

Filters follow the same syntax as JSON-API:

```
?filters[name_of_filter]=value

?filters[search]=term
?filters[created_before]=2020-01-01
?filters[user]=ed69bc2f-58ef-44bd-bfdb-5adcac43b6b9
```

Filters may also accept an array of values:

```
?filters[status][]=draft&filters[status][]=published
```

## Sorting

Sorting accepts one or many fields, together with an optional modifier. The
modifier defaults to "asc" for ascending order.

```
?sort=name.asc
?sort=name
?sort=id.desc,name.asc
```

Sorting may also be an array:

```
?sort[]=name.asc&sort[]=id.desc
```
