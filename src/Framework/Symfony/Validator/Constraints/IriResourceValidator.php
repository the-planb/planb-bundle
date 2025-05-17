<?php
declare(strict_types=1);

namespace PlanB\Framework\Symfony\Validator\Constraints;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;


class IriResourceValidator extends ConstraintValidator
{

    private RouterInterface $router;
    private ResourceMetadataCollectionFactoryInterface $factory;


    public function __construct(RouterInterface $router, ResourceMetadataCollectionFactoryInterface $factory)
    {
        $this->router = $router;
        $this->factory = $factory;
    }


    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IriResource) {
            throw new UnexpectedTypeException($constraint, IriResource::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }


        $pattern = $this->getIriPattern($constraint->resourceClass);

        if (!preg_match($pattern, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setParameter('{{ resourceClass }}', $constraint->resourceClass)
                ->addViolation();
            return;
        }
    }

    private function getIriPattern(string $entityClass): string
    {
        $metadata = $this->factory->create($entityClass);
        $resource = $metadata->getOperation();

        $route = $this->router->generate($resource->getName(), [
            'id' => 'X'
        ]);

        $route = preg_quote($route, '/');
        $route = preg_replace('/X/', '[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}', $route);

        return "/{$route}/Di";

    }
}
