<?php

namespace Presta\MailReceiverBundle\Rule;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Rule\Action\RuleActionHandlingException;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;

final class RuleMatcher
{
    /**
     * @var ConditionRegistry
     */
    private $conditions;

    public function __construct(ConditionRegistry $conditions)
    {
        $this->conditions = $conditions;
    }

    public function match(Email $email, Rule $rule, RuleExecution $execution): bool
    {
        return $this->evaluate(
            $this->vote($email, $rule, $execution),
            $rule->getConditionOperator()
        );
    }

    /**
     * @param array<bool> $votes
     */
    private function evaluate(array $votes, string $operator): bool
    {
        foreach ($votes as $vote) {
            if ($operator === Rule::OPERATOR_OR && $vote === true) {
                return true; // at least one vote is positive to success OR evaluation
            }
            if ($operator === Rule::OPERATOR_AND && $vote === false) {
                return false; // at least one vote is negative to fail AND evaluation
            }
        }

        return $operator === Rule::OPERATOR_AND ? true : false;
    }

    /**
     * @return array<bool>
     */
    private function vote(Email $email, Rule $rule, RuleExecution $execution): array
    {
        $votes = [];

        foreach ($rule->getConditions() as $ruleCondition) {
            $condition = $this->conditions->get($ruleCondition->getType());

            $error = null;
            $vote = false;

            try {
                $vote = $condition->satisfy($email, $ruleCondition->getSettings());
            } catch (RuleActionHandlingException $exception) {
                $error = $exception;
            }

            $votes[] = $vote;

            if ($vote) {
                $execution->satisfiedCondition($ruleCondition);
            } else {
                $execution->notSatisfiedCondition($ruleCondition, $error);
            }
        }

        return $votes;
    }
}
