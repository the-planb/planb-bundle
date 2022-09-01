<?php

declare(strict_types=1);

namespace PlanB\Framework\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

abstract class Compound extends Composite
{
    /** @var Constraint[] */
    public $constraints = [];

    public function __construct(mixed $options = null)
    {
        if (isset($options[$this->getCompositeOption()])) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'You can\'t redefine the "%s" option. Use the "%s::getConstraints()" method instead.',
                    $this->getCompositeOption(),
                    __CLASS__
                )
            );
        }

        $this->constraints = $this->getConstraints($this->normalizeOptions($options));

        parent::__construct($options);
    }

    final protected function getCompositeOption(): string
    {
        return 'constraints';
    }

    /**
     * @return Constraint[]
     */
    abstract protected function getConstraints(array $options): array;

    final public function validatedBy(): string
    {
        return CompoundValidator::class;
    }

    abstract public function getClassName(): string;
}
