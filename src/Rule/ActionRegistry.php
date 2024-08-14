<?php

namespace Presta\MailReceiverBundle\Rule;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ActionNotRegisteredException;

final class ActionRegistry
{
    /**
     * @param array<string, RuleActionInterface> $actions
     */
    public function __construct(private array $actions)
    {
    }

    /**
     * @return string[]
     */
    public function list(): array
    {
        return array_keys($this->actions);
    }

    /**
     * @param string $code
     *
     * @return RuleActionInterface
     * @throws ActionNotRegisteredException
     */
    public function get(string $code): RuleActionInterface
    {
        if (!isset($this->actions[$code])) {
            throw ActionNotRegisteredException::code($code);
        }

        return $this->actions[$code];
    }
}
