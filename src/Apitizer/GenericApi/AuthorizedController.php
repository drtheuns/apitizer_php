<?php

namespace Apitizer\GenericApi;

use Apitizer\QueryBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthorizedController extends Controller
{
    use AuthorizesRequests;

    protected function setup(): void
    {
        parent::setup();

        $this->authorizeResource($this->queryBuilder->model());
    }
}
