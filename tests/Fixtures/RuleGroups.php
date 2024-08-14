<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Fixtures;

use Presta\MailReceiverBundle\Entity\RuleGroup;

class RuleGroups
{
    public static function create(
        string $name,
        array $rules = [],
    ): RuleGroup {
        $group = new RuleGroup();
        $group->setName($name);
        foreach ($rules as $rule) {
            $group->addRulesElement($rule);
        }

        return $group;
    }
}
