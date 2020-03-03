<?php

namespace Apitizer\Parser;

class Context
{
    /**
     * @var string The output buffer to which the string are appended for the
     * current context;
     */
    public $accumulator = '';

    /**
     * A place to hold information regarding the current context.
     *
     * In the case of field parsing, this might be the fields up until now.
     *
     * @var Relation|ParsedInput
     */
    public $stack;

    /**
     * The parent context.
     *
     * Each subexpression gets their own context with a reference to their
     * parent context. This allows expressions such as:
     *
     * "id,name,comments(id,body)"
     *
     * to have their own context for the inner braces in the "comments".
     *
     * @var Context|null
     */
    public $parent = null;

    /**
     * Quoted expressions are string that were wrapped with double quotes ("").
     *
     * Inside of quoted expressions, meta characters such as , and ( ) are
     * ignored until the quote is closed.
     *
     * @var bool
     */
    public $isQuoted = false;

    /**
     * @param Relation|ParsedInput $stack
     */
    public function __construct($stack, Context $parent = null)
    {
        $this->stack = $stack;
        $this->parent = $parent;
    }

    public function makeChildContext(Relation $relation): Context
    {
        return new self($relation, $this);
    }

    public function addField(string $field): void
    {
        $this->stack->addField($field);
    }

    public function addRelation(Relation $relation): void
    {
        $this->stack->addRelation($relation);
    }
}
