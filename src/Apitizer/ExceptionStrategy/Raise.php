<?php

namespace Apitizer\ExceptionStrategy;

use Apitizer\Schema;
use Apitizer\Exceptions\ApitizerException;

class Raise implements Strategy
{
    public function handle(
        Schema $schema,
        ApitizerException $apitizerException
    ): void {
        throw $apitizerException;
    }
}
