<?php

namespace Apitizer;

class Apidoc
{
    protected $queryBuilder;

    protected $fields = [];

    protected $sorts = [];

    protected $filters = [];

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        $this->setFields($queryBuilder->getFields());
        $this->setSorts($queryBuilder->getSorts());
        $this->setFilters($queryBuilder->getFilters());

        $queryBuilder->apidoc($this);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getSorts()
    {
        return $this->sorts;
    }

    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }
}
