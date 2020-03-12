<?php

namespace Apitizer\ExceptionStrategy;

use Apitizer\Exceptions\ApitizerException;
use Apitizer\Schema;

/**
 * Exception strategies are responsible for handling exceptions generated by the
 * schema, primarily from user input. For example, if the filter receives
 * unexpected input and throws an exception, the strategy determines whether to
 * ignore this error and continue, raise an error, or perhaps alert an error
 * tracker.
 *
 * There are two strategies out of the box:
 *
 * - Raise: reraise the exception. This is the default strategy because it often
 *    makes the most sense to have the caller fix their request before
 *    continuing. For example, if a filter fails but we continue execution by
 *    ignoring the filter, the user might get unexpected results.
 * - Ignore: ignore the exception and continue execution.
 */
interface Strategy
{
    public function handle(Schema $schema, ApitizerException $apitizerException): void;
}
