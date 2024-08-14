<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Model;

use DateTimeImmutable;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Model\GroupExecution;

class Execution
{
    /**
     * @var GroupExecution[]
     */
    private array $groupExecutions = [];

    private DateTimeImmutable $date;

    public function __construct(private Email $email)
    {
        $this->date = new DateTimeImmutable();
    }

    /**
     * @return GroupExecution[]
     */
    public function getGroupExecutions(): array
    {
        return $this->groupExecutions;
    }

    /**
     * @param GroupExecution[] $groupExecutions
     */
    public function setGroupExecutions(array $groupExecutions): void
    {
        $this->groupExecutions = $groupExecutions;
    }

    public function addGroupExecution(GroupExecution $groupExecution): void
    {
        $this->groupExecutions[] = $groupExecution;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
