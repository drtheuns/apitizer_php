<?php

namespace Apitizer\Types;

class FilterTypePicker
{

    /** @var Filter $filter */
    protected $filter;

    /** @var string $format */
    protected $format;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Set the filter type
     * @return Filter
     */
    public function string(): Filter
    {
        $this->filter->setType('string');
        return $this->filter;
    }

    /**
     * Set the filter type
     * @return Filter
     */
    public function uuid(): Filter
    {
        $this->filter->setType('uuid');
        return $this->filter;
    }

    /**
     * Set the filter type
     * @return Filter
     */
    public function boolean(): Filter
    {
        $this->filter->setType('boolean');
        return $this->filter;
    }

    /**
     * Set the filter type
     * @return Filter
     */
    public function number(): Filter
    {
        $this->filter->setType('number');
        return $this->filter;
    }

    /**
     * Set the filter type
     * @return Filter
     */
    public function integer(): Filter
    {
        $this->filter->setType('integer');
        return $this->filter;
    }

    /**
     * Set the filter type
     * @param string $format formatting date
     * @return Filter
     */
    public function date(string $format = null): Filter
    {
        $this->filter->setType('string');
        $this->format = $format;
        return $this->filter;
    }

    /**
     * Set the filter type
     * @param string $format formatting datetime
     * @return Filter
     */
    public function datetime(string $format = null): Filter
    {
        $this->filter->setType('date');
        $this->format = $format;
        return $this->filter;
    }

    /**
     * Set the filter type
     * @param array<string> $values 
     * @return Filter
     */
    public function enum(array $values): Filter
    {
        $this->filter->setType('enum');
        return $this->filter;
    }

    public function array(): Filter
    {
        $this->filter->setType('array');
        return $this->filter;
    }
    
}