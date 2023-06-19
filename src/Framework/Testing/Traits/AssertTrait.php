<?php

namespace PlanB\Framework\Testing\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @method assertEquals(mixed $value, mixed $stored, string $string)
 */
trait AssertTrait
{
    public function assertObjectProperties(object $object, array $expected)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        foreach ($expected as $key => $value) {
            $stored = $propertyAccessor->getValue($object, $key);
            $this->assertEquals($value, $stored, "Property Name: ${key}");
        }
    }
}
