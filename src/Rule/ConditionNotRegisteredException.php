<?php

namespace Presta\MailReceiverBundle\Rule;

use InvalidArgumentException;

final class ConditionNotRegisteredException extends InvalidArgumentException
{
    public static function code(string $code): self
    {
        return new self(sprintf('Condition "%s" is not registered.', $code));
    }
}
