<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Presta\MailReceiverBundle\Tests\DatabaseTestHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

abstract class CommandTestCase extends KernelTestCase
{
    protected static ?EntityManagerInterface $doctrine = null;

    protected function setUp(): void
    {
        parent::setUp();

        self::$doctrine = self::getContainer()->get('doctrine.orm.default_entity_manager');
        DatabaseTestHelper::rebuild(self::getContainer());
    }

    /**
     * @param class-string<Command> $command
     */
    protected static function tester(string $command): CommandTester
    {
        return new CommandTester(self::getContainer()->get($command));
    }
}
