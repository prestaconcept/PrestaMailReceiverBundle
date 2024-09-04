<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Presta\MailReceiverBundle\Entity\RuleGroup;

/**
 * @extends ServiceEntityRepository<RuleGroup>
 */
class RuleGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RuleGroup::class);
    }
}
