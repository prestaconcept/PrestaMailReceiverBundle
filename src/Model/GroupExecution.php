<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Model;

use Presta\MailReceiverBundle\Entity\RuleGroup;
use Presta\MailReceiverBundle\Model\Execution;
use Presta\MailReceiverBundle\Model\RuleExecution;

class GroupExecution
{
    /**
     * @var RuleExecution[]
     */
    private array $ruleExecutions = [];

    public function __construct(private RuleGroup $ruleGroup, private Execution $execution)
    {
    }

    public function getExecution(): Execution
    {
        return $this->execution;
    }

    public function getRuleGroup(): RuleGroup
    {
        return $this->ruleGroup;
    }

    /**
     * @return RuleExecution[]
     */
    public function getRuleExecutions(): array
    {
        return $this->ruleExecutions;
    }

    public function addRuleExecution(RuleExecution $ruleExecution): void
    {
        $this->ruleExecutions[] = $ruleExecution;
    }
}
