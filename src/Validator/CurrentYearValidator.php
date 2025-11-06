<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CurrentYearValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CurrentYear) {
            throw new UnexpectedTypeException($constraint, CurrentYear::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $currentYear = (int) date('Y');

        if ($value > $currentYear) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ current_year }}', $currentYear)
                ->addViolation();
        }
    }
}
