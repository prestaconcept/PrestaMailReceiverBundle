<?php

namespace Presta\MailReceiverBundle\Rule\Action;

use InvalidArgumentException;
use Presta\MailReceiverBundle\Rule\Action\RuleActionHandlingException;

final class InvalidSettingsException extends InvalidArgumentException implements RuleActionHandlingException
{
    /**
     * @param array<string, mixed> $settings
     */
    public static function missing(string $expected, array $settings): self
    {
        return new self(
            sprintf('Missing setting "%s", %s given.', $expected, json_encode(array_keys($settings)))
        );
    }
}
