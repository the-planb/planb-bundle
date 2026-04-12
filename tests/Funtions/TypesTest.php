<?php

declare(strict_types=1);

namespace PlanB\Tests\Funtions;

use PHPUnit\Framework\TestCase;

final class TypesTest extends TestCase
{
    public function test_is_of_the_type()
    {
        $this->assertTrue(is_of_the_type(1, 'int'));
        $this->assertTrue(is_of_the_type(1, 'integer'));
        $this->assertTrue(is_of_the_type(1.1, 'float'));
        $this->assertTrue(is_of_the_type(1.1, 'double'));
        $this->assertTrue(is_of_the_type('foo', 'string'));
        $this->assertTrue(is_of_the_type([1, 2], 'array'));
        $this->assertTrue(is_of_the_type(new \stdClass(), 'object'));
        $this->assertTrue(is_of_the_type(true, 'bool'));
        $this->assertTrue(is_of_the_type(null, 'null'));
        $this->assertTrue(is_of_the_type(fn() => 'foo', 'callable'));
        $this->assertTrue(is_of_the_type([1, 2], 'countable'));
        $this->assertTrue(is_of_the_type([1, 2], 'iterable'));
        $this->assertTrue(is_of_the_type(new \stdClass(), \stdClass::class));
        $this->assertTrue(is_of_the_type(opendir('.'), 'resource'));

        $this->assertTrue(is_of_the_type(1, 'string', 'int'));
        $this->assertFalse(is_of_the_type(1, 'string', 'bool'));

        $this->assertTrue(is_of_the_type(1, 'mixed'));
        $this->assertTrue(is_of_the_type(1));
    }

    public function test_type_of()
    {
        $resource = fopen('php://memory', 'r');
        fclose($resource);

        $this->assertSame('integer', type_of(1));
        $this->assertSame('string', type_of('foo'));
        $this->assertSame('array', type_of([1, 2]));
        $this->assertSame('stdClass', type_of(new \stdClass()));
        $this->assertSame('null', type_of(null));
        $this->assertSame('callable', type_of(fn() => 'foo'));
        $this->assertSame('boolean', type_of(true));
        $this->assertSame('double', type_of(1.1));
        $this->assertSame('resource', type_of($resource));
    }
}
