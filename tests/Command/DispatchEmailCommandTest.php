<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Command;

use Presta\MailReceiverBundle\Command\DispatchEmailCommand;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\Execution;
use Presta\MailReceiverBundle\Entity\RuleGroupElement;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleActions;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleConditions;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleGroups;
use Presta\MailReceiverBundle\Tests\Fixtures\Rules;

class DispatchEmailCommandTest extends CommandTestCase
{
    public function test(): void
    {
        // Given
        self::$doctrine->persist($group = RuleGroups::create('PHPUnit Group'));
        self::$doctrine->persist($rule = Rules::create('PHPUnit Rule'));
        $group->addRulesElement($element = new RuleGroupElement($rule, $group));
        $element->setSortOrder(1);
        $element->setBreakpoint(false);
        $rule->addCondition(RuleConditions::create('RecipientMatch', [
            'operator' => MatchSettings::OPERATOR_MATCH,
            'value' => '.+@prestaconcept.net',
        ]));
        $rule->addAction(RuleActions::create('Forward', ['to' => 'forward@prestaconcept.net']));
        self::$doctrine->persist($email = Emails::cheerz());
        $email->setStatus(Email::STATUS_WAITING);
        self::$doctrine->flush();
        self::assertSame(0, self::$doctrine->getRepository(Execution::class)->count([]));

        // When
        $tester = self::tester(DispatchEmailCommand::class);
        $code = $tester->execute([]);

        // Then
        self::assertSame(DispatchEmailCommand::SUCCESS, $code);
        $email = self::$doctrine->find(Email::class, $email->getId());
        self::assertSame(Email::STATUS_TREATED, $email->getStatus());
        self::assertSame(1, self::$doctrine->getRepository(Execution::class)->count([]));
    }
}
