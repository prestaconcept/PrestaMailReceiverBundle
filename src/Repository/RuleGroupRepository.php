<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Presta\MailReceiverBundle\Entity\RuleGroup;

/**
 * @extends EntityRepository<RuleGroup>
 */
class RuleGroupRepository extends EntityRepository
{
}
