<?php

namespace Apitizer\Parser;

interface Parser
{
    public function parse(RawInput $input): ParsedInput;
}
