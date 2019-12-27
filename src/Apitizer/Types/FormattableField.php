<?php

namespace Apitizer\Types;

interface FormattableField
{
    public function format(string $format): Field;
}
