<?php
declare(strict_types=1);

namespace PlanB\Tests\Framework\Symfony\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Symfony\Validator\Constraints\IriResource;
use Symfony\Component\Validator\Constraint;

class IriResourceTest extends TestCase
{
    public function test_it_throws_an_exception_when_constraint_has_not_resouce_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        new IriResource([]);
    }

    public function test_it_has_the_correct_target()
    {
        $constraint = new IriResource('resourceClass');
    
        $this->assertEquals(Constraint::PROPERTY_CONSTRAINT, $constraint->getTargets());
    }
}
