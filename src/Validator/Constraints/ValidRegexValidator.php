<?php

namespace Presta\MailReceiverBundle\Validator\Constraints;

use Presta\MailReceiverBundle\Validator\Constraints\ValidRegex;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidRegexValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidRegex) {
            throw new UnexpectedTypeException($constraint, ValidRegex::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (@preg_match(sprintf('{%s}', $value), '') === false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(ValidRegex::IS_INVALID_ERROR)
                ->addViolation();
        }
    }
}
