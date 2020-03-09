# Sorting

Sorting is essentially the same as filters, but instead they are used to
determine the ordering of the data.

## Defining sorts

```
use Apitizer\Types\Sort;
use Illuminate\Database\Eloquent\Builder;

public function sorts(): array
{
    return [
        // Sort the query by column 'name'.
        'name' => $this->sort()->byField('name'),
        
        // Sort the query using a custom callback
        'id' => $this->sort()->handleUsing(function (Builder $query, Sort $sort) {
            $query->orderBy('id', $sort->getOrder());
        }),
        
        // Sort using an invokable class.
        'title' => $this->sort()->handleUsing(new TitleSort),
    ];
}
```
