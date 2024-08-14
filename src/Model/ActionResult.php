<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Model;

use LogicException;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Throwable;

class ActionResult
{
    private Throwable|null $error;

    public function __construct(
        private RuleExecution $execution,
        private RuleAction $action,
        private string $result,
        Throwable $exception = null
    ) {
        if ($action->getType() === null) {
            throw new LogicException('Action has no type');
        }

        $this->error = $exception;
    }

    public function getExecution(): RuleExecution
    {
        return $this->execution;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }

    public function getAction(): RuleAction
    {
        return $this->action;
    }
}
