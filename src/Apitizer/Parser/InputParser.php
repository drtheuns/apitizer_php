<?php

namespace Apitizer\Parser;

use Apitizer\Parser\Context;
use Apitizer\Parser\Relation;
use Apitizer\Parser\Sort;
use Illuminate\Support\Arr;

/**
 * The request parser is responsible for turning the request data that we
 * received from the client into something that can be interpreted by the
 * rest of the schema.
 */
class InputParser implements Parser
{
    public function parse(RawInput $rawInput): ParsedInput
    {
        $parsedInput = new ParsedInput();

        $this->parseFields($parsedInput, $rawInput->getFields());
        $this->parseFilters($parsedInput, $rawInput->getFilters());
        $this->parseSorts($parsedInput, $rawInput->getSorts());

        return $parsedInput;
    }

    /**
     * @param ParsedInput $parsedInput
     * @param string|string[]|mixed $rawFields
     */
    public function parseFields(ParsedInput $parsedInput, $rawFields): void
    {
        // Input examples:
        //   id,name
        //   id,"first,name",comments(id,"wo)(,-w")
        if (empty($rawFields)) {
            $parsedInput->fields = [];
            return;
        }

        if (\is_array($rawFields)) {
            $parsedInput->fields = $rawFields;
            return;
        }

        if (! is_string($rawFields)) {
            $parsedInput->fields = [];
            return;
        }

        $context = new Context($parsedInput);

        if (! $characters = $this->stringToArray($rawFields)) {
            $parsedInput->fields = [];
            return;
        }

        foreach ($characters as $character) {
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
                // This can be empty when, for example, the comma follows the
                // ending of a relation and the accumulator has already been cleared:
                // "id,comments(author(id),id)"
                //                     ---^
                if (! empty($context->accumulator)) {
                    $context->addField($context->accumulator);
                    $context->accumulator = '';
                }
                continue 2;
            case '(':
                // We've encountered a relationship. Parse everything until ")"
                // into a new context after which we revert the context back to
                // the parent.
                $relation = new Relation($context->accumulator);
                $context = $context->makeChildContext($relation);
                continue 2;
            case ')':
                if (! empty($context->accumulator)) {
                    // Add remainder to the current stack.
                    $context->addField($context->accumulator);
                }

                // For phpstan to understand that parent is filled at this
                // point, and the stack is a relation.
                assert($context->parent !== null);
                assert($context->stack instanceof Relation);

                // Add the current stack (a relation) to the parent.
                $context->parent->addRelation($context->stack);

                // Cleanup and return context to the parent.
                $context = $context->parent;
                $context->accumulator = '';
                continue 2;
            default:
                $context->accumulator .= $character;
            }
        }

        // If there is still some remainder in the accumulator, assume that it's
        // a field. For example: "id,name" will still have "name" in the
        // accumulator when the string ends.
        if (! empty($context->accumulator)) {
            $context->addField($context->accumulator);
        }
    }

    /**
     * @param ParsedInput $parsedInput
     * @param mixed|array<string, mixed>|null $rawFilters
     */
    public function parseFilters(ParsedInput $parsedInput, $rawFilters): void
    {
        // We expect filters to be in the format of:
        // filters[search]=query
        // which means the filters must always be an (assoc) array.
        if (! is_array($rawFilters) || ! Arr::isAssoc($rawFilters)) {
            $parsedInput->filters = [];
            return;
        }

        $parsedInput->filters = $rawFilters;
    }

    /**
     * @param ParsedInput $parsedInput
     * @param mixed|string[]|string $rawSorts
     */
    public function parseSorts(ParsedInput $parsedInput, $rawSorts): void
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
            $parsedInput->sorts = [];
            return;
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

        $parsedInput->sorts = $sorts;
    }

    /**
     * @return string[]|false
     */
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
