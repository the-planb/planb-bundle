<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Symfony\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Symfony\Validator\Constraints\Compound;
use PlanB\Framework\Symfony\Validator\Constraints\CompoundValidator;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

final class CompoundValidatorTest extends TestCase
{
    /**
     * @dataProvider valuesProvider
     */
    public function test_it_validates_if_a_value_object_is_passed(mixed $value, int $violationsCount)
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($value, new CompoundExample());

        $this->assertSame($violationsCount, $violations->count());
    }

    public function valuesProvider()
    {
        return [
            [12, 1],
            [3, 1],
            [8, 0],
            [new ValueObjectExample(8), 0],
        ];
    }

    public function test_constraint_throws_an_exception_when_constratints_options_is_passed()
    {
        $this->expectException(ConstraintDefinitionException::class);
        new CompoundExample([
            'constraints' => [],
        ]);
    }

    public function test_validator_throws_an_exception_when_an_invalid_constratint_is_passed()
    {
        $this->expectException(UnexpectedTypeException::class);

        $validator = new CompoundValidator();
        $validator->validate(3, new Range(['min' => 4]));
    }
}

final class  CompoundExample extends Compound
{

    protected function getConstraints(array $options): array
    {
        return [
            new Range([
                'min' => 5,
                'max' => 10,
            ]),
        ];
    }

    public function getClassName(): string
    {
        return ValueObjectExample::class;
    }
}

final class ValueObjectExample
{
    public function __construct(int $value)
    {
    }
}