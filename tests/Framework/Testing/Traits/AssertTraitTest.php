<?php

namespace PlanB\Tests\Framework\Testing\Traits;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Testing\Traits\AssertTrait;

class AssertTraitTest extends TestCase
{
    use AssertTrait;

    public function testAssertObjectProperties()
    {

        $data = ['uno' => 1, 'dos' => 2];
        $object = new \stdClass();
        $object->uno = 1;
        $object->dos = 2;

        $this->assertObjectProperties($object, $data);
    }

    public function testAssertObjectPropertiesFailsProperly()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/Property Name: dos/');
        $this->expectExceptionMessageMatches('/Failed asserting that 2 matches expected 3./');

        $data = ['uno' => 1, 'dos' => 3];
        $object = new \stdClass();
        $object->uno = 1;
        $object->dos = 2;

        $this->assertObjectProperties($object, $data);

    }
}
