<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Command;

use DateTimeImmutable;
use Presta\MailReceiverBundle\Repository\EmailRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ArchiveEmailCommand extends Command
{
    /**
     * @param array<string, string> $archive
     */
    public function __construct(
        private EmailRepository $emails,
        private array $archive,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('presta:mail-receiver:archive')
            ->setDescription('Archive emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->archive as $status => $since) {
            $count = $this->emails->delete($status, $date = (new DateTimeImmutable())->modify($since));

            $this->logger->log(
                $count > 0 ? LogLevel::INFO : LogLevel::DEBUG,
                'Removed emails matching archive config rules.',
                ['status' => $status, 'since' => $since, 'date' => $date->format(DATE_ISO8601), 'count' => $count]
            );
        }

        return self::SUCCESS;
    }
}
