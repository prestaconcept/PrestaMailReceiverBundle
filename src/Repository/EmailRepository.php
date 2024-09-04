<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Repository;

use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Presta\MailReceiverBundle\Entity\Email;

/**
 * @extends ServiceEntityRepository<Email>
 */
final class EmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

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
