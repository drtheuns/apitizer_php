<?php

namespace Apitizer\ExceptionStrategy;

use Apitizer\Schema;
use Apitizer\Exceptions\ApitizerException;

class Ignore implements Strategy
{
    public function handle(
        Schema $schema,
        ApitizerException $apitizerException
    ): void {
        //
    }
}
