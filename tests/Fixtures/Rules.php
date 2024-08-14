<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Entity\RuleCondition;

class Rules
{
    /**
     * @param array<RuleCondition> $conditions
     * @param array<RuleAction>    $actions
     */
    public static function create(
        string $name,
        string $operator = Rule::OPERATOR_AND,
        array $conditions = [],
        array $actions = [],
    ): Rule {
        $rule = new Rule();
        $rule->setName($name);
        $rule->setConditionOperator($operator);
        foreach ($conditions as $condition) {
            $rule->addCondition($condition);
        }
        foreach ($actions as $action) {
            $rule->addAction($action);
        }

        return $rule;
    }
}
