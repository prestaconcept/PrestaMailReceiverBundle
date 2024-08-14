<?php

namespace Presta\MailReceiverBundle\Storage;

use Doctrine\Persistence\ObjectManager;
use Presta\MailReceiverBundle\Entity\Execution as ExecutionEntity;
use Presta\MailReceiverBundle\Model\Execution;

final class ExecutionStorage
{
    public function __construct(private ObjectManager $objectManager)
    {
    }

    public function store(Execution $execution): void
    {
        foreach ($execution->getGroupExecutions() as $groupExecution) {
            foreach ($groupExecution->getRuleExecutions() as $ruleExecution) {
                $this->objectManager->persist(
                    ExecutionEntity::fromModel($execution->getEmail(), $execution->getDate(), $ruleExecution),
                );
            }
        }
        $this->objectManager->flush();
    }
}
