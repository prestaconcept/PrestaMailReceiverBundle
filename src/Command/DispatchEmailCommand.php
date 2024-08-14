<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Command;

use Presta\MailReceiverBundle\Dispatcher\MailDispatcher;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Repository\EmailRepository;
use Presta\MailReceiverBundle\Storage\ExecutionStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DispatchEmailCommand extends Command
{
    public function __construct(
        private EmailRepository $emailRepository,
        private MailDispatcher $mailDispatcher,
        private ExecutionStorage $executionStorage,
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('presta:mail-receiver:dispatch')
            ->setDescription('Dispatch waiting emails to matching rules actions.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $emailsToTreat = $this->emailRepository->findBy(['status' => Email::STATUS_WAITING]);

        if (count($emailsToTreat) === 0) {
            return 0;
        }

        foreach ($emailsToTreat as $email) {
            $execution = $this->mailDispatcher->dispatchEmail($email);
            $this->executionStorage->store($execution);
        }

        return self::SUCCESS;
    }
}
