<?php

declare(strict_types=1);

namespace PlanB\Framework\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CompoundValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof Compound) {
            throw new UnexpectedTypeException($constraint, Compound::class);
        }

        if (is_a($value, $constraint->getClassName())) {
            return;
        }

        $context = $this->context;

        $validator = $context->getValidator()->inContext($context);

        $validator->validate($value, $constraint->constraints);
    }
}
