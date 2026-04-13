<?php

declare(strict_types=1);

namespace PlanB\Tests\Funtions;

use PHPUnit\Framework\TestCase;

final class ArraysTest extends TestCase
{
    public function test_iterable_to_array()
    {
        $this->assertSame([1, 2, 3], iterable_to_array([1, 2, 3]));

        $generator = (function () {
            yield 1;
            yield 2;
            yield 3;
        })();
        $this->assertSame([1, 2, 3], iterable_to_array($generator));

        $this->assertSame(['a', 'b'], iterable_to_array(['k1' => 'a', 'k2' => 'b'], false));
    }

    public function test_array_flatten()
    {
        $input = [1, [2, [3, 4]], 5];
        $this->assertSame([1, 2, 3, 4, 5], array_flatten($input));
        $this->assertSame([1, 2, [3, 4], 5], array_flatten($input, 1));
    }

    public function test_array_collapse()
    {
        $input = [
            'user' => [
                'name' => 'John',
                'address' => [
                    'city' => 'New York',
                ],
            ],
            'active' => true,
        ];

        $expected = [
            'user/name' => 'John',
            'user/address/city' => 'New York',
            'active' => true,
        ];

        $this->assertSame($expected, array_collapse($input, PHP_INT_MAX, '/'));
    }

    public function test_cartesian_product()
    {
        $a = [1, 2];
        $b = ['a', 'b'];

        $expected = [
            [1, 'a'],
            [1, 'b'],
            [2, 'a'],
            [2, 'b'],
        ];

        $this->assertSame($expected, cartesian_product($a, $b));
    }
}
