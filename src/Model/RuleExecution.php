<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Model;

use Presta\MailReceiverBundle\Entity\ExecutionActionResult;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Entity\RuleCondition;
use Throwable;

class RuleExecution
{
    private bool $satisfied = false;

    private bool $performed = false;

    /**
     * @var Evaluation[]
     */
    private array $evaluations = [];

    /**
     * @var ActionResult[]
     */
    private array $results = [];

    public function __construct(private Rule $rule, private GroupExecution $groupExecution)
    {
    }

    public function isSatisfied(): bool
    {
        return $this->satisfied;
    }

    public function setSatisfied(bool $satisfied): void
    {
        $this->satisfied = $satisfied;
    }

    public function isPerformed(): bool
    {
        return $this->performed;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function getGroupExecution(): GroupExecution
    {
        return $this->groupExecution;
    }

    /**
     * @return Evaluation[]
     */
    public function getEvaluations(): array
    {
        return $this->evaluations;
    }

    /**
     * @return ActionResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function performedAction(RuleAction $action): void
    {
        $this->results[] = new ActionResult($this, $action, ExecutionActionResult::RESULT_SUCCESS);
        $this->performed = true;
    }

    public function failedAction(RuleAction $action, Throwable $error): void
    {
        $this->results[] = new ActionResult($this, $action, ExecutionActionResult::RESULT_FAILED, $error);
    }

    public function skippedAction(RuleAction $action): void
    {
        $this->results[] = new ActionResult($this, $action, ExecutionActionResult::RESULT_SKIPPED);
    }

    public function satisfiedCondition(RuleCondition $condition): void
    {
        $this->evaluations[] = new Evaluation($this, $condition, true);
    }

    public function notSatisfiedCondition(RuleCondition $condition, ?Throwable $error): void
    {
        $this->evaluations[] = new Evaluation($this, $condition, false, $error);
    }
}
