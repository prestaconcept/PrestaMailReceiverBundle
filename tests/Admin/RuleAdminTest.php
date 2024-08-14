<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Admin;

use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Tests\Fixtures\Rules;

class RuleAdminTest extends AdminTestCase
{
    public function testList(): void
    {
        // Given
        self::$doctrine->persist(Rules::create('PHPUnit Rule'));
        self::$doctrine->flush();

        // When
        $page = self::$client->request('GET', '/presta/mailreceiver/rule/list');

        // Then
        self::assertResponseIsSuccessful();
        self::assertPageContainsCountElement(1, $page);
    }

    public function testCreate(): void
    {
        // Given
        self::assertSame(0, self::$doctrine->getRepository(Rule::class)->count([]));

        // When
        $page = self::$client->request('GET', '/presta/mailreceiver/rule/create');
        self::submitSonataMainForm($page, [
            'name' => 'PHPUnit Rule',
        ]);

        // Then
        self::assertResponseRedirects();
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSame(1, self::$doctrine->getRepository(Rule::class)->count([]));
    }

    public function testAddCondition(): void
    {
        // Given
        self::$doctrine->persist($rule = Rules::create('PHPUnit Rule'));
        self::$doctrine->flush();
        self::assertCount(0, $rule->getConditions());

        // When
        self::$client->request('GET', '/presta/mailreceiver/rule/' . $rule->getId() . '/conditions/add/BodyMatch');

        // Then
        self::assertResponseRedirects('/presta/mailreceiver/rule/' . $rule->getId() . '/edit');
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        $rule = self::$doctrine->find(Rule::class, $rule->getId());
        self::assertCount(1, $rule->getConditions());
    }

    public function testAddAction(): void
    {
        // Given
        self::$doctrine->persist($rule = Rules::create('PHPUnit Rule'));
        self::$doctrine->flush();
        self::assertCount(0, $rule->getActions());

        // When
        self::$client->request('GET', '/presta/mailreceiver/rule/' . $rule->getId() . '/actions/add/Forward');

        // Then
        self::assertResponseRedirects('/presta/mailreceiver/rule/' . $rule->getId() . '/edit');
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        $rule = self::$doctrine->find(Rule::class, $rule->getId());
        self::assertCount(1, $rule->getActions());
    }
}
