<?php

namespace Apitizer\Types;

/**
 * The fetch specification holds all the validated information that was
 * requested by the client.
 */
class FetchSpec
{
    /**
     * The fields that should be fetched from the datasource.
     *
     * @var Field[]|Association[]
     */
    protected $fields = [];

    /**
     * The sorting methods that should be applied.
     *
     * @var Sort[]
     */
    protected $sorts = [];

    /**
     * The filters that should be applied to the datasource.
     *
     * @var Filter[]
     */
    protected $filters = [];

    public function __construct(array $fields, array $sorts, array $filters)
    {
        $this->fields = $fields;
        $this->sorts = $sorts;
        $this->filters = $filters;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function getSorts()
    {
        return $this->sorts;
    }

    public function getFilters()
    {
        return $this->filters;
    }
}
