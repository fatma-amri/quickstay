<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function getTotalRevenue(): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->setParameter('status', Payment::STATUS_COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getRevenueThisMonth(): float
    {
        $startOfMonth = new \DateTime('first day of this month');
        $startOfMonth->setTime(0, 0, 0);

        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->andWhere('p.paidAt >= :startOfMonth')
            ->setParameter('status', Payment::STATUS_COMPLETED)
            ->setParameter('startOfMonth', $startOfMonth)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getRevenueByMonth(int $months = 12): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $months = max(1, (int) $months);

        $sql = "
            SELECT 
                DATE_FORMAT(paid_at, '%Y-%m') as month,
                COALESCE(SUM(amount), 0) as revenue
            FROM payments
            WHERE status = 'completed'
            AND paid_at >= DATE_SUB(NOW(), INTERVAL {$months} MONTH)
            GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
