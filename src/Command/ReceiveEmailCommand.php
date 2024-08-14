<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Command;

use Doctrine\Persistence\ObjectManager;
use Presta\MailReceiverBundle\Entity\Email;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReceiveEmailCommand extends Command
{
    public function __construct(private ObjectManager $manager)
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('presta:mail-receiver:receive')
            ->addArgument('filename', InputArgument::OPTIONAL, 'File to get content from')
            ->setDescription('Receive email content and store it.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $contents = '';
            if ($filename = $input->getArgument('filename')) {
                $contents = file_get_contents($filename);
            } else {
                while (!feof(STDIN)) {
                    $contents .= fread(STDIN, 1024);
                }
            }

            if ($contents === '') {
                throw new \RuntimeException("Please provide a filename or pipe template content to STDIN.");
            }

            $email = Email::fromRaw($contents);

            $this->manager->persist($email);
            $this->manager->flush();
        } catch (\Throwable $exception) {
            if (!$output->isQuiet()) {
                throw $exception;
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
