<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Presta\MailReceiverBundle\Entity\Execution;

/**
 * @extends EntityRepository<Execution>
 */
final class ExecutionRepository extends EntityRepository
{
}
