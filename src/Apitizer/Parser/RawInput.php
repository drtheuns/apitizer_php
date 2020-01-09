<?php

namespace Apitizer\Parser;

use Apitizer\Apitizer;
use Illuminate\Http\Request;

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
