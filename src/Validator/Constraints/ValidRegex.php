<?php

namespace Presta\MailReceiverBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidRegex extends Constraint
{
    public const IS_INVALID_ERROR = 'b6486e6a-5c47-4ae5-af8f-6c6657ff0771';

    protected static $errorNames = [
        self::IS_INVALID_ERROR => 'IS_INVALID_ERROR',
    ];

    public string $message = 'This regex is not valid.';
}
