<?php

namespace Tests\Unit\Support;

use Apitizer\ExceptionStrategy\Raise;
use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Parser\InputParser;
use Apitizer\Parser\RawInput;
use Apitizer\Support\FetchSpecFactory;
use Tests\Support\Schemas\EmptySchema;
use Tests\Unit\TestCase;

class FetchSpecFactoryTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_when_an_unknown_filter_is_called(): void
    {
        $this->expectException(InvalidInputException::class);

        $input = ['filters' => ['name' => 'term']];
        $parsedInput = (new InputParser)->parse(RawInput::fromArray($input));
        $schema = EmptySchema::make()->setExceptionStrategy(new Raise);

        FetchSpecFactory::fromRequestInput($parsedInput, $schema);
    }

    /** @test */
    public function it_throws_an_exception_when_an_unknown_sort_is_called(): void
    {
        $this->expectException(InvalidInputException::class);

        $input = ['sorts' => 'id.asc'];
        $parsedInput = (new InputParser)->parse(RawInput::fromArray($input));
        $schema = EmptySchema::make()->setExceptionStrategy(new Raise);

        FetchSpecFactory::fromRequestInput($parsedInput, $schema);
    }
}
