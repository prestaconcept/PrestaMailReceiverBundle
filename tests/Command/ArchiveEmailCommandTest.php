<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Command;

use Presta\MailReceiverBundle\Command\ArchiveEmailCommand;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;

class ArchiveEmailCommandTest extends CommandTestCase
{
    public function test(): void
    {
        // Given
        self::$doctrine->persist($email = Emails::cheerz());
        $email->setStatus(Email::STATUS_TREATED);
        self::$doctrine->flush();
        self::assertSame(1, self::$doctrine->getRepository(Email::class)->count([]));

        // When
        $tester = self::tester(ArchiveEmailCommand::class);
        $code = $tester->execute([]);

        // Then
        self::assertSame(ArchiveEmailCommand::SUCCESS, $code);
        self::assertSame(0, self::$doctrine->getRepository(Email::class)->count([]));
    }
}
