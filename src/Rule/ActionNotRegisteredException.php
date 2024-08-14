<?php

namespace Presta\MailReceiverBundle\Rule;

use InvalidArgumentException;

final class ActionNotRegisteredException extends InvalidArgumentException
{
    public static function code(string $code): self
    {
        return new self(sprintf('Action "%s" is not registered.', $code));
    }
}
