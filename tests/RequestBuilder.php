<?php

namespace Tests;

use Apitizer\Apitizer;
use Illuminate\Http\Request;

/**
 * Helper class for tests to quickly build requests with fields, filters, sorts.
 */
class RequestBuilder
{
    protected $url = 'users';
    protected $method = 'GET';
    protected $fields;
    protected $filters = [];
    protected $sorts;
    protected $limit;
    protected $user;

    public function __construct(string $method = null, string $url = null)
    {
        $this->method = $method ?? $this->method;
        $this->url = $url ?? $this->url;
    }

    public function fields($fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function filters($filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function filter(string $key, $value): self
    {
        $this->filters[$key] = $value;

        return $this;
    }

    public function sort($sorts): self
    {
        $this->sorts = $sorts;

        return $this;
    }

    public function to(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function user($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function make(): Request
    {
        $queryParams = [];

        if ($this->fields) {
            $queryParams[Apitizer::getFieldKey()] = $this->fields;
        }

        if (! empty($this->filters)) {
            $queryParams[Apitizer::getFilterKey()] = $this->filters;
        }

        if ($this->sorts) {
            $queryParams[Apitizer::getSortKey()] = $this->sorts;
        }

        if (! is_null($this->limit)) {
            $queryParams['limit'] = $this->limit;
        }

        $request = Request::create($this->url, $this->method);
        $request->merge($queryParams);
        $request->setUserResolver(function () {
            return $this->user;
        });

        return $request;
    }
}
