<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use Presta\MailReceiverBundle\Entity\RuleAction;

class RuleActions
{
    public static function create(
        string $type,
        array $settings = [],
        int $sort = 0,
        bool $breakpoint = false,
    ): RuleAction {
        $action = new RuleAction();
        $action->setType($type);
        $action->setSettings($settings);
        $action->setSortOrder($sort);
        $action->setBreakpoint($breakpoint);

        return $action;
    }
}
