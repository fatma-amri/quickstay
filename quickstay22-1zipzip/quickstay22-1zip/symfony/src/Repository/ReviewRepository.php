<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findApprovedByProperty(int $propertyId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.property = :propertyId')
            ->andWhere('r.isApproved = :approved')
            ->setParameter('propertyId', $propertyId)
            ->setParameter('approved', true)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingReviews(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.isApproved = :approved')
            ->setParameter('approved', false)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAverageRatingByProperty(int $propertyId): ?float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as avgRating')
            ->where('r.property = :propertyId')
            ->andWhere('r.isApproved = :approved')
            ->setParameter('propertyId', $propertyId)
            ->setParameter('approved', true)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round((float)$result, 1) : null;
    }

    public function countReviews(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPendingReviews(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.isApproved = :approved')
            ->setParameter('approved', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
