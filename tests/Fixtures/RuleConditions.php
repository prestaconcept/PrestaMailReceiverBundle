<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use Presta\MailReceiverBundle\Entity\RuleCondition;

class RuleConditions
{
    public static function create(
        string $type,
        array $settings = [],
        int $sort = 0,
    ): RuleCondition {
        $condition = new RuleCondition();
        $condition->setType($type);
        $condition->setSettings($settings);
        $condition->setSortOrder($sort);

        return $condition;
    }
}
