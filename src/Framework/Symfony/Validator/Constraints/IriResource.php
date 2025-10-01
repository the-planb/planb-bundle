<?php

declare(strict_types=1);

namespace PlanB\Framework\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class IriResource extends Constraint
{
    public string $resourceClass;
    public string $message = 'The value {{ value }} is not a valid IRI for a resource of type {{ resourceClass }}.';

    public function __construct($options = null, ?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        if (\is_string($options)) {
            $options = ['resourceClass' => $options];
        }

        $options = (array)$options;

        parent::__construct($options, $groups, $payload);

        if (!isset($this->resourceClass)) {
            throw new \InvalidArgumentException(sprintf('The "%s" constraint requires the "resourceClass" option to be set.', __CLASS__));
        }

        $this->message = $message ?? $this->message;
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function getDefaultOption(): string
    {
        return 'resourceClass';
    }

}
