<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Command;

use Presta\MailReceiverBundle\Command\ReceiveEmailCommand;
use Presta\MailReceiverBundle\Entity\Email;

class ReceiveEmailCommandTest extends CommandTestCase
{
    public function test(): void
    {
        // Given
        self::assertSame(0, self::$doctrine->getRepository(Email::class)->count([]));

        // When
        $tester = self::tester(ReceiveEmailCommand::class);
        $code = $tester->execute(['filename' => __DIR__ . '/../Fixtures/cheerz.eml']);

        // Then
        self::assertSame(ReceiveEmailCommand::SUCCESS, $code);
        self::assertSame(1, self::$doctrine->getRepository(Email::class)->count([]));
    }
}
