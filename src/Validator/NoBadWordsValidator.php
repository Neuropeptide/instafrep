<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoBadWordsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint NoBadWords */

        if (null === $value || '' === $value) {
            return;
        }

        // L'implémentation de cet algo est à améliorer
        foreach ($constraint->forbiddenWords as $badWord) {
            if (strpos(strtolower($value), $badWord) !== false) {

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }

    }
}
