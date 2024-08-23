<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Presta\MailReceiverBundle\Entity\Execution;

/**
 * @extends ServiceEntityRepository<Execution>
 */
final class ExecutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Execution::class);
    }
}
