<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Symfony\Validator\Constraints;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Symfony\Validator\Constraints\IriResource;
use PlanB\Framework\Symfony\Validator\Constraints\IriResourceValidator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class IriResourceValidatorTest extends TestCase
{
    #[DataProvider('valuesProvider')]
    public function test_it_validates_a_iri_properly(mixed $value, int $violationsCount)
    {
        $validator = $this->createValidator();

        $context = $this->createMock(ExecutionContext::class);
        $context->expects($this->exactly($violationsCount))
            ->method('buildViolation');

        $validator->initialize($context);

        $validator->validate($value, new IriResource('entityName'));
    }

    public static function valuesProvider(): array
    {
        return [
            ['/api/module/authors/0196de4a-f1ea-b710-74a2-c04529f243b6', 0],
            ['/api/module/authors/bad-uuid', 1],
            ['/api/module/other-resource/0196de4a-f1ea-b710-74a2-c04529f243b6', 1],
            ['cualquier-cosa', 1],
            ['', 0],
        ];
    }

    public function test_it_throws_an_exception_when_constraint_arg_is_invalid()
    {
        $this->expectException(UnexpectedTypeException::class);

        $constraint = $this->createMock(Constraint::class);
        $validator = $this->createValidator();
        $validator->validate('input', $constraint);
    }

    public function test_it_throws_an_exception_when_value_arg_is_invalid()
    {
        $this->expectException(UnexpectedValueException::class);

        $constraint = $this->createMock(IriResource::class);
        $validator = $this->createValidator();
        $validator->validate(234, $constraint);
    }


    /**
     * @return IriResourceValidator
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function createValidator(): IriResourceValidator
    {
        $entityName = 'entityName';
        $operationName = 'operationName';

        $operation = $this->createMock(Operation::class);
        $operation->method('getName')
            ->willReturn($operationName);

        $metadata = $this->createMock(ResourceMetadataCollection::class);
        $metadata->method('getOperation')
            ->willReturn($operation);

        $factory = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $factory->method('create')
            ->with($this->equalTo($entityName))
            ->willReturn($metadata);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')
            ->with($this->equalTo($operationName), $this->isArray())
            ->willReturn('/api/module/authors/X');

        return new IriResourceValidator($router, $factory);
    }

}
