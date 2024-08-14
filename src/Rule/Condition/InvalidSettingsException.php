<?php

namespace Presta\MailReceiverBundle\Rule\Condition;

use Presta\MailReceiverBundle\Rule\Condition\RuleConditionSatisfyException;

final class InvalidSettingsException extends \InvalidArgumentException implements RuleConditionSatisfyException
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
