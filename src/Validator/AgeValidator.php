<?php

namespace App\Validator;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Age $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $today = new DateTimeImmutable();
        $diff = $today->diff($value);

        if ($diff->y < 18){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->format('m-d-y'))
                ->addViolation();
        }

        
    }
}
