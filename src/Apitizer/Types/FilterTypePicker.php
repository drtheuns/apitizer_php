<?php

namespace Apitizer\Types;

class FilterTypePicker
{

    /** @var Filter $filter */
    protected $filter;

    /** @var null|string $format */
    protected $format;

    /**
     * @var array<string> array with all the available enumerators.
     */
    protected $enums = null;

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
    public function float(): Filter
    {
        $this->filter->setType('float');
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
     *
     * @param null|string $format the format for date(time) value. Defaults to
     * 'Y-m-d' for dates, and 'Y-m-d H:i:s' for datetimes.
     *
     * @return Filter
     */
    public function date(string $format = null): Filter
    {
        $this->filter->setType('date');
        if ($format != null) {
            $this->filter->setFormatting($format);
        }
        return $this->filter;
    }

    /**
     * Set the filter type
     *
     * @param null|string $format the format for date(time) value. Defaults to
     * 'Y-m-d' for dates, and 'Y-m-d H:i:s' for datetimes.
     *
     * @return Filter
     */
    public function datetime(string $format = null): Filter
    {
        $this->filter->setType('datetime');
        if ($format != null) {
            $this->filter->setFormatting($format);
        }
        return $this->filter;
    }

    /**
     * Set the enum type
     *
     * @param array<string> $enums
     *
     * @return Filter
     */
    public function enum(array $enums): Filter
    {
        $this->filter->setType('enum');
        $this->filter->setEnumerators($enums);
        return $this->filter;
    }

    /**
     * Set the array type
     *
     * @return Filter
     */
    public function array(): Filter
    {
        $this->filter->setExpectArray(true);
        $this->filter->setType('array');
        return $this->filter;
    }

    /**
     * Set the any type
     *
     * @return Filter
     */
    public function any(): Filter
    {
        $this->filter->setType('any');
        return $this->filter;
    }
}
