<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use DateTimeImmutable;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\Execution;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleGroup;
use Presta\MailReceiverBundle\Model\Execution as ExecutionModel;
use Presta\MailReceiverBundle\Model\GroupExecution;
use Presta\MailReceiverBundle\Model\RuleExecution;

class Executions
{
    public static function success(
        RuleGroup $group,
        Rule $rule,
        Email $email,
        DateTimeImmutable $date = null,
    ): Execution {
        $execution = new RuleExecution($rule, new GroupExecution($group, new ExecutionModel($email)));
        foreach ($rule->getConditions() as $condition) {
            $execution->satisfiedCondition($condition);
        }
        foreach ($rule->getActions() as $action) {
            $execution->performedAction($action);
        }

        return Execution::fromModel($email, $date ?? new DateTimeImmutable(), $execution);
    }
}
