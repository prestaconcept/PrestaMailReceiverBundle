<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Repository;

use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Presta\MailReceiverBundle\Entity\Email;

/**
 * @extends EntityRepository<Email>
 */
final class EmailRepository extends EntityRepository
{
    public function delete(string $status, DateTimeInterface $since): int
    {
        return (int)$this->getEntityManager()->createQueryBuilder()
            ->delete($this->getClassName(), 'email')
            ->where('email.status = :status')
            ->andWhere('email.sentAt <= :date')
            ->setParameter('status', $status)
            ->setParameter('date', $since)
            ->getQuery()
            ->execute();
    }
}
