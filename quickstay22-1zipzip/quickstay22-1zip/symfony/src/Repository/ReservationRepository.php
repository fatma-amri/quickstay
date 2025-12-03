<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function getReservationsByMonth(int $months = 12): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $months = max(1, (int) $months);
        
        $sql = "
            SELECT 
                TO_CHAR(DATE_TRUNC('month', created_at), 'YYYY-MM') as month,
                COUNT(*) as count
            FROM reservations
            WHERE created_at >= NOW() - MAKE_INTERVAL(months => {$months})
            GROUP BY DATE_TRUNC('month', created_at)
            ORDER BY month ASC
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }

    public function getReservationsByStatus(): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.status, COUNT(r.id) as count')
            ->groupBy('r.status');
        
        $results = $qb->getQuery()->getResult();
        
        $statusCounts = [];
        foreach ($results as $result) {
            $statusCounts[$result['status']] = $result['count'];
        }
        
        return $statusCounts;
    }

    public function countCurrentlyOccupiedProperties(): int
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.property)')
            ->where('r.startDate <= :today')
            ->andWhere('r.endDate >= :today')
            ->andWhere('r.status = :status')
            ->setParameter('today', $today)
            ->setParameter('status', Reservation::STATUS_CONFIRMED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function checkAvailability(int $propertyId, \DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $excludeReservationId = null): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.property = :propertyId')
            ->andWhere('r.status IN (:statuses)')
            ->andWhere('(r.startDate <= :endDate AND r.endDate >= :startDate)')
            ->setParameter('propertyId', $propertyId)
            ->setParameter('statuses', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
        
        if ($excludeReservationId) {
            $qb->andWhere('r.id != :excludeId')
               ->setParameter('excludeId', $excludeReservationId);
        }
        
        return $qb->getQuery()->getSingleScalarResult() === 0;
    }

    public function findByUserWithProperty(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.property', 'p')
            ->addSelect('p')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
