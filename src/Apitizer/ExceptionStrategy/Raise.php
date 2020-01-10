<?php

namespace Apitizer\ExceptionStrategy;

use Apitizer\QueryBuilder;
use Apitizer\Exceptions\ApitizerException;

class Raise implements Strategy
{
    public function handle(
        QueryBuilder $queryBuilder,
        ApitizerException $apitizerException
    ): void
    {
        throw $apitizerException;
    }
}
