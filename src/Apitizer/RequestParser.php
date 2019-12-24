<?php

namespace Apitizer;

use Apitizer\Parser\Context;
use Apitizer\Parser\Relation;
use Illuminate\Http\Request;

/**
 * The request parser is responsible for turning the request data that we
 * received from the client, into something that can be interpreted by the
 * rest of the query builder.
 */
class RequestParser
{
    public function parse(Request $request): RequestInput
    {
        $requestData = new RequestInput();
        $requestData->fields = $this->parseFields($request);
        $requestData->filters = $this->parseFilters($request);
        $requestData->sorts = $this->parseSorts($request);

        return $requestData;
    }

    /**
     * @return (string|Relation)[]
     */
    public function parseFields(Request $request): array
    {
        $rawFields = $request->input('fields', '');

        if (empty($rawFields)) {
            return [];
        }

        if (is_array($rawFields)) {
            throw new \Exception('cannot handle that yet');
        }

        $context = new Context();

        // TODO: Add line/column numbers for debugging
        foreach ($this->stringToArray($rawFields) as $character) {
            if ($context->isQuoted && $character !== '"') {
                $context->accumulator .= $character;
                continue;
            }

            switch ($character) {
            case '"':
                $context->isQuoted = ! $context->isQuoted;
                continue 2;
            case ',':
                if ($context->isQuoted) {
                    $context->accumulator .= $character;
                } else {
                    $context->stack[] = $context->accumulator;
                    $context->accumulator = '';
                }
                continue 2;
            case '(':
                // We've encountered a relationship. Parse everything until ")"
                // into a new context after which we revert the context back to
                // the parent.
                $context = $context->makeChildContext();
                continue 2;
            case ')':
                // Add remainder to the current stack.
                $context->stack[] = $context->accumulator;

                // The parent's accumulator currently holds anything up until
                // the (, which should be the relationship name
                $context->parent->stack[] = new Relation($context->parent->accumulator, $context->stack);
                $context->parent->accumulator = '';

                $context = $context->parent;
                continue 2;
            default:
                $context->accumulator .= $character;
            }
        }

        // If there is still some remainder in the accumulator, assume that it's
        // a field. For example: "id,name" will still have "name" in the
        // accumulator when the string ends.
        if (! empty($context->accumulator)) {
            $context->stack[] = $context->accumulator;
        }

        return $context->stack;
    }

    public function parseFilters(Request $request): array
    {
        return [];
    }

    public function parseSorts(Request $request): array
    {
        return [];
    }

    protected function stringToArray(string $raw)
    {
        return preg_split('//u', $raw, null, PREG_SPLIT_NO_EMPTY);
    }
}
