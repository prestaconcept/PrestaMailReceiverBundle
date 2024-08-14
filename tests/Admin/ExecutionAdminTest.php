<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Admin;

use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\Executions;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleActions;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleConditions;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleGroups;
use Presta\MailReceiverBundle\Tests\Fixtures\Rules;

class ExecutionAdminTest extends AdminTestCase
{
    public function testList(): void
    {
        // Given
        self::$doctrine->persist($group = RuleGroups::create('Group'));
        self::$doctrine->persist($rule = Rules::create('Rule'));
        $rule->addCondition(RuleConditions::create('RecipientMatch', [
            'operator' => MatchSettings::OPERATOR_MATCH,
            'value' => '*@prestaconcept.net',
        ]));
        $rule->addAction(RuleActions::create('Forward', ['to' => 'forward@prestaconcept.net']));
        self::$doctrine->persist($email = Emails::cheerz());
        self::$doctrine->persist(Executions::success($group, $rule, $email));
        self::$doctrine->flush();

        // When
        $page = self::$client->request('GET', '/presta/mailreceiver/execution/list');

        // Then
        self::assertResponseIsSuccessful();
        self::assertPageContainsCountElement(1, $page);
    }
}
