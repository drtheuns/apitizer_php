<?php

namespace Apitizer\Parser;

use Apitizer\Apitizer;
use Illuminate\Http\Request;

class RawInput
{
    /**
     * @var mixed
     */
    protected $fields;

    /**
     * @var mixed
     */
    protected $filters;

    /**
     * @var mixed
     */
    protected $sorts;

    /**
     * @param mixed $fields
     * @param mixed $filters
     * @param mixed $sorts
     */
    public function __construct($fields, $filters, $sorts)
    {
        $this->fields = $fields;
        $this->filters = $filters;
        $this->sorts = $sorts;
    }

    public static function fromRequest(Request $request): self
    {
        return new static(
            $request->input(Apitizer::getFieldKey(), ''),
            $request->input(Apitizer::getFilterKey(), []),
            $request->input(Apitizer::getSortKey(), [])
        );
    }

    /**
     * @param array{fields?: string|string[], filters?: array<string, mixed>,
     *              sorts?: string|string[]} $input
     */
    public static function fromArray(array $input): self
    {
        return new static(
            $input['fields'] ?? '',
            $input['filters'] ?? [],
            $input['sorts'] ?? []
        );
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return mixed
     */
    public function getSorts()
    {
        return $this->sorts;
    }
}
