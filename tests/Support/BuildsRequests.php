<?php

namespace Tests\Support;

use Illuminate\Http\Request;

trait BuildsRequests
{
    protected function buildRequest($queryParameters = [], $resource = 'users', $method = 'GET'): Request
    {
        $request = Request::create($resource, $method);
        $request->merge($queryParameters);

        return $request;
    }
}
