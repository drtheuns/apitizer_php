<?php

namespace Apitizer\Parser;

use Apitizer\Parser\Context;
use Apitizer\Parser\Relation;
use Apitizer\Parser\Sort;
use Illuminate\Support\Arr;

/**
 * The request parser is responsible for turning the request data that we
 * received from the client into something that can be interpreted by the
 * rest of the query builder.
 */
class InputParser implements Parser
{
    public function parse(RawInput $rawInput): ParsedInput
    {
        $parsedInput = new ParsedInput();
        $parsedInput->fields = $this->parseFields($rawInput->getFields());
        $parsedInput->filters = $this->parseFilters($rawInput->getFilters());
        $parsedInput->sorts = $this->parseSorts($rawInput->getSorts());
        return $parsedInput;
    }

    /**
     * @param string|array $fields
     * @return (string|Relation)[]
     */
    public function parseFields($rawFields): array
    {
        // Input examples:
        //   id,name
        //   id,"first,name",comments(id,"wo)(,-w")
        if (empty($rawFields)) {
            return [];
        }

        if (\is_array($rawFields)) {
            return $rawFields;
        }

        $context = new Context();

        foreach ($this->stringToArray($rawFields) as $character) {
            if ($context->isQuoted && $character !== '"') {
                $context->accumulator .= $character;
                continue;
            }

            // Ignore whitespace in non-quoted expressions.
            if ($this->isBlacklistedCharacter(mb_ord($character))) {
                continue;
            }

            switch ($character) {
            case '"':
                $context->isQuoted = ! $context->isQuoted;
                continue 2;
            case ',':
                $context->stack[] = $context->accumulator;
                $context->accumulator = '';
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

    /**
     * @param array|null $rawFilters
     */
    public function parseFilters($rawFilters): array
    {
        // We expect filters to be in the format of:
        // filters[search]=query
        // which means the filters must always be an (assoc) array.
        if (! is_array($rawFilters) || ! Arr::isAssoc($rawFilters)) {
            return [];
        }

        return $rawFilters;
    }

    /**
     * @param array|string $rawSorts
     */
    public function parseSorts($rawSorts): array
    {
        // Sort input examples:
        //   "name"
        //   "name.desc"
        //   ["first_name.desc", "last_name.asc"]
        //   first_name.desc,last_name.asc
        if (is_string($rawSorts)) {
            $rawSorts = explode(',', $rawSorts);
        }

        if (! is_array($rawSorts)) {
            // We cannot parse this, ignore the given sorting.
            return [];
        }

        $sorts = [];

        foreach ($rawSorts as $rawSort) {
            if (! is_string($rawSort)) {
                continue;
            }

            $rawSort = trim($rawSort);
            $pos = mb_strpos($rawSort, '.');

            if ($pos === false) {
                $sorts[] = new Sort($rawSort, Sort::ASC);
                continue;
            }

            $field = mb_substr($rawSort, 0, $pos);
            $order = mb_substr($rawSort, $pos + 1);

            if (empty($order) || ! in_array($order, [Sort::ASC, Sort::DESC])) {
                $order = Sort::ASC;
            }

            $sorts[] = new Sort($field, $order);
        }

        return $sorts;
    }

    protected function stringToArray(string $raw)
    {
        return preg_split('//u', $raw, null, PREG_SPLIT_NO_EMPTY);
    }

    protected function isBlacklistedCharacter(int $character): bool
    {
        // This is just a best-attempt, probably not good enough.
        return
            // Control characters
            $character < 33

            // Spacing: https://unicode-table.com/en/blocks/general-punctuation/
            || ($character > 8191 && $character < 8208)

            // Line separator, formatting, etc. Same category as above: "General punctuation"
            || ($character > 8231 && $character < 8240)

            // NO-BREAK SPACE
            || $character === 160

            // MEDIUM MATHEMATICAL SPACE
            || $character === 8287;
    }
}
