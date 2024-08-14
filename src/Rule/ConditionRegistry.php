<?php

namespace Presta\MailReceiverBundle\Rule;

use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Presta\MailReceiverBundle\Rule\ConditionNotRegisteredException;

final class ConditionRegistry
{
    /**
     * @param array<string, RuleConditionInterface> $conditions
     */
    public function __construct(private array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return string[]
     */
    public function list(): array
    {
        return array_keys($this->conditions);
    }

    /**
     * @param string $code
     *
     * @return RuleConditionInterface
     * @throws ConditionNotRegisteredException
     */
    public function get(string $code): RuleConditionInterface
    {
        if (!isset($this->conditions[$code])) {
            throw ConditionNotRegisteredException::code($code);
        }

        return $this->conditions[$code];
    }
}
