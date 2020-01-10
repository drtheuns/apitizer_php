<?php

namespace Apitizer\Parser;

class Context
{
    /**
     * The output buffer to which the string are appended for the current context;
     */
    public $accumulator = '';

    /**
     * A place to hold information regarding the current context.
     *
     * In the case of field parsing, this might be the fields up until now.
     */
    public $stack = [];

    /**
     * The parent context.
     *
     * Each subexpression gets their own context with a reference to their
     * parent context. This allows expressions such as:
     *
     * "id,name,comments(id,body)"
     *
     * to have their own context for the inner braces in the "comments".
     */
    public $parent = null;

    /**
     * Quoted expressions are string that were wrapped with double quotes ("").
     *
     * Inside of quoted expressions, meta characters such as , and ( ) are
     * ignored until the quote is closed.
     */
    public $isQuoted = false;

    public function __construct(Context $parent = null) {
        $this->parent = $parent;
    }

    public function makeChildContext(): self
    {
        return new self($this);
    }
}
