<?php

namespace Tests\Unit\Exceptions;

use Apitizer\Exceptions\InvalidOutputException;
use Tests\Unit\TestCase;
use Tests\Feature\Models\User;

class InvalidOutputExceptionTest extends TestCase
{
    /** @test */
    public function it_can_generate_references_to_data_with_identifiers()
    {
        $id = 1;
        $idArray = ['id' => $id];
        $idObj = (object) $idArray;
        $uuid = '399f48b9-ed9a-48b7-be49-c6ef11910b12';
        $uuidArray = ['uuid' => $uuid];
        $uuidObj = (object) $uuidArray;

        $this->assertEquals($id, InvalidOutputException::rowReference($idArray));
        $this->assertEquals($id, InvalidOutputException::rowReference($idObj));
        $this->assertEquals($uuid, InvalidOutputException::rowReference($uuidArray));
        $this->assertEquals($uuid, InvalidOutputException::rowReference($uuidObj));
    }

    /** @test */
    public function it_can_generate_a_reference_for_eloquent_models()
    {
        $user = new User();
        $user->id = $id = 1;

        $this->assertEquals($id, InvalidOutputException::rowReference($user));
    }
}
