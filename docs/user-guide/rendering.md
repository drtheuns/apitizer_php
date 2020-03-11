# Rendering

The `Renderer` component determines what the output will be. By default, the
fetched data will be rendered to a set of nested objects. There are several
renders available out of the box and are documented below. There are several
ways of changing the renderer:

- Change the `Apitizer\Rendering\Renderer` in the Laravel container:

```php
use Apitizer\Rendering\Renderer;
use Apitizer\Rendering\BasicRenderer;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Renderer::class, BasicRenderer::class);
    }
}
```

- Override the `getRenderer` on the query builder:

```php
use Apitizer\Rendering\Renderer;
use Apitizer\Rendering\ReferenceMapRenderer;

class PostBuilder extends QueryBuilder
{
    public function getRenderer(): Renderer
    {
        return new ReferenceMapRenderer
    }
}
```

- Use the `setRenderer` method on an instance:

```php
PostBuilder::make($request)
    ->setRenderer(new JsonApiRenderer)
    ->paginate()
```

## `BasicRenderer`

This is the default renderer. Each association will be rendered as a nested
object in the parent:

```
?fields=id,comments(id,author(id))
```

```json
{
  "id": 1,
  "comments": [
    {
      "id": 1,
      "author": {
        "id": 1
      }
    }
  ]
}
```

## `JsonApiRenderer`

This renderer emulates a JSON-API response. Note that JSON-API requires there to
always be an `id` and a `type`. You can implement the
`Apitizer\JsonApi\Resource` to change these values. Refer to
[JSON-API](https://jsonapi.org) for more information.

```
?fields=title,author(name),comments(body,author(name))
```

```json
{
  "data": [
    {
      "type": "post",
      "id": "1",
      "attributes": {
        "title": "How to change rendering?"
      },
      "relationships": {
        "author": {
          "data": {"type": "user", "id": "1"}
        },
        "comments": {
          "data": [
            {"type": "comment", "id": "1"}
          ]
        }
      }
    }
  ],
  "included": [
    {
      "type": "user",
      "id": "1",
      "attributes": {
        "name": "John Doe"
      }
    },
    {
      "type": "comment",
      "id": "1",
      "attributes": {
        "body": "I comment on my own articles"
      },
      "relationships": {
        "author": {
          "data": {"type": "user", "id": "1"}
        }
      }
    }
  ]
}
```

## `ReferenceMapRenderer`

The ReferenceMap is based on JSON-API, but the `included` is an object that
links the `type` and `id` in way that is easier to parse.

```json
{
  "data": [
    {
      "type": "post",
      "id": "1",
      "attributes": {
        "title": "How to change rendering?"
      },
      "relationships": {
        "author": {
          "data": {"type": "user", "id": "1"}
        },
        "comments": {
          "data": [
            {"type": "comment", "id": "1"}
          ]
        }
      }
    }
  ],
  "included": {
    "user": {
      "1": {
        "type": "user",
        "id": "1",
        "attributes": {
          "name": "John Doe"
        }
      }
    },
    "comment": {
      "1": {
        "type": "comment",
        "id": "1",
        "attributes": {
          "body": "I comment on my own articles"
        },
        "relationships": {
          "author": {
            "data": {"type": "user", "id": "1"}
          }
        }
      }
    }
  }
}
```
