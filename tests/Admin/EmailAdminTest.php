<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Admin;

use Presta\MailReceiverBundle\Tests\Fixtures\Emails;

class EmailAdminTest extends AdminTestCase
{
    public function testList(): void
    {
        // Given
        self::$doctrine->persist(Emails::cheerz());
        self::$doctrine->flush();

        // When
        $page = self::$client->request('GET', '/presta/mailreceiver/email/list');

        // Then
        self::assertResponseIsSuccessful();
        self::assertPageContainsCountElement(1, $page);
    }
}
