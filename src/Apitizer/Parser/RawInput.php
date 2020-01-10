<?php

namespace Apitizer\Parser;

use Apitizer\Apitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RawInput
{
    protected $fields;
    protected $filters;
    protected $sorts;

    public function __construct($fields, $filters, $sorts)
    {
        $this->fields = $fields;
        $this->filters = $filters;
        $this->sorts = $sorts;
    }

    public static function fromRequest(Request $request)
    {
        return new static(
            $request->input(Apitizer::getFieldKey(), ''),
            $request->input(Apitizer::getFilterKey(), []),
            $request->input(Apitizer::getSortKey(), [])
        );
    }

    public static function fromArray(array $input)
    {
        return new static(
            Arr::get($input, 'fields', ''),
            Arr::get($input, 'filters', []),
            Arr::get($input, 'sorts', [])
        );
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getSorts()
    {
        return $this->sorts;
    }
}
