<?php

namespace Tests\Feature;

use App\Rules\ValidateLatitude;
use App\Rules\ValidateLongitude;
use Tests\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function a_set_of_longitude_coordinates_can_be_validated()
    {
        $rule = ['location' => [new ValidateLongitude()]];
        $this->assertTrue(validator(['location' => '1'], $rule)->passes());
        $this->assertTrue(validator(['location' => '1.0'], $rule)->passes());
        $this->assertTrue(validator(['location' => '90.0'], $rule)->passes());
        $this->assertTrue(validator(['location' => '90.0'], $rule)->passes());
        $this->assertFalse(validator(['location' => '91.0'], $rule)->passes());
        $this->assertTrue(validator(['location' => '-2.132500'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.0364335'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.03643357'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.0364335'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.036433576'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.0364335'], $rule)->passes());
        $this->assertFalse(validator(['location' => 'asd'], $rule)->passes());
        $this->assertFalse(validator(['location' => null], $rule)->passes());
    }
    /** @test */
    public function a_set_of_latitude_coordinates_can_be_validated()
    {
        $rule = ['location' => [new ValidateLatitude()]];
        $this->assertTrue(validator(['location' => '1'], $rule)->passes());
        $this->assertTrue(validator(['location' => '1.0'], $rule)->passes());
        $this->assertTrue(validator(['location' => '90.0'], $rule)->passes());
        $this->assertTrue(validator(['location' => '90.0'], $rule)->passes());
        $this->assertFalse(validator(['location' => '91.0'], $rule)->passes());
        $this->assertTrue(validator(['location' => '-2.132500'], $rule)->passes());
        $this->assertTrue(validator(['location' => '57.129406'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.0364335'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.03643357'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.0364335'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.036433576'], $rule)->passes());
        $this->assertFalse(validator(['location' => '-77.0364335'], $rule)->passes());
        $this->assertFalse(validator(['location' => 'asd'], $rule)->passes());
        $this->assertFalse(validator(['location' => null], $rule)->passes());
    }
}
