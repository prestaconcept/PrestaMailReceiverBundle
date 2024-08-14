<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Admin;

use Presta\MailReceiverBundle\Entity\RuleGroup;
use Presta\MailReceiverBundle\Tests\Fixtures\RuleGroups;

class RuleGroupAdminTest extends AdminTestCase
{
    public function testList(): void
    {
        // Given
        self::$doctrine->persist(RuleGroups::create('PHPUnit Group'));
        self::$doctrine->flush();

        // When
        $page = self::$client->request('GET', '/presta/mailreceiver/rulegroup/list');

        // Then
        self::assertResponseIsSuccessful();
        self::assertPageContainsCountElement(1, $page);
    }

    public function testCreate(): void
    {
        // Given
        self::assertSame(0, self::$doctrine->getRepository(RuleGroup::class)->count([]));

        // When
        $page = self::$client->request('GET', '/presta/mailreceiver/rulegroup/create');
        self::submitSonataMainForm($page, [
            'name' => 'PHPUnit Group',
        ]);

        // Then
        self::assertResponseRedirects();
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSame(1, self::$doctrine->getRepository(RuleGroup::class)->count([]));
    }
}
