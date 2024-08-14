<?php

namespace Presta\MailReceiverBundle\Rule;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\ExecutionActionResult;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Psr\Log\LogLevel;

class ApplyRule
{
    public const LOG_LEVEL_STATUS_MAP = [
        Email::STATUS_TREATED => LogLevel::INFO,
        Email::STATUS_UNMATCHED => LogLevel::NOTICE,
        Email::STATUS_ERRORED => LogLevel::ERROR,
    ];

    public function __construct(
        private RuleMatcher $ruleMatcher,
        private RuleDispatcher $ruleDispatcher
    ) {
    }

    public function applyRule(Rule $rule, Email $email, RuleExecution $execution): string
    {
        if (!$this->ruleMatcher->match($email, $rule, $execution)) {
            foreach ($rule->getActions() as $ruleAction) {
                $execution->skippedAction($ruleAction);
            }

            return Email::STATUS_UNMATCHED;
        }

        $execution->setSatisfied(true);

        $this->ruleDispatcher->dispatch($email, $rule, $execution);

        foreach ($execution->getResults() as $result) {
            if ($result->getResult() === ExecutionActionResult::RESULT_FAILED) {
                return Email::STATUS_ERRORED;
            }
        }

        return Email::STATUS_TREATED;
    }
}
