<?php
declare(strict_types=1);

namespace PlanB\Tests\Domain\Input;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Input\Input;

class InputTest extends TestCase
{
    public function test_it_can_be_created_from_an_array()
    {
        $name = 'pepe';
        $lastName = 'gonzalez';

        $input = $this->createInput([
            'name' => $name,
            'lastName' => $lastName,
        ]);

        $this->assertSame($name, $input->name);
        $this->assertSame($lastName, $input->lastName);
        $this->assertNull($input->getId());
    }

    public function test_it_throws_an_exception_when_a_property_does_not_exits()
    {
        $this->expectExceptionMessageMatches("/^(El atributo name2 no existe en.*)/");

        $this->createInput([
            'name2' => 'xxxx',
        ]);
    }

    private function createInput(array $data): Input
    {
        return new class($data) extends Input {

            public string $name;
            public string $lastName;

            public function toArray(): array
            {
                return [];
            }
        };

    }
}
