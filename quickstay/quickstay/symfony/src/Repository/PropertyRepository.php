<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function findPublishedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->andWhere('p.isAvailable = :available')
            ->setParameter('status', Property::STATUS_PUBLISHED)
            ->setParameter('available', true)
            ->orderBy('p.createdAt', 'DESC');
    }

    public function findPublished(): array
    {
        return $this->findPublishedQueryBuilder()->getQuery()->getResult();
    }

    public function findFeatured(int $limit = 6): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->andWhere('p.isAvailable = :available')
            ->andWhere('p.isFeatured = :featured')
            ->setParameter('status', Property::STATUS_PUBLISHED)
            ->setParameter('available', true)
            ->setParameter('featured', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchProperties(array $filters): QueryBuilder
    {
        $qb = $this->findPublishedQueryBuilder();

        if (!empty($filters['city'])) {
            $qb->andWhere('LOWER(p.city) LIKE LOWER(:city)')
               ->setParameter('city', '%' . $filters['city'] . '%');
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('p.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['category'])) {
            $qb->andWhere('p.category = :category')
               ->setParameter('category', $filters['category']);
        }

        if (!empty($filters['minPrice'])) {
            $qb->andWhere('p.price >= :minPrice')
               ->setParameter('minPrice', $filters['minPrice']);
        }

        if (!empty($filters['maxPrice'])) {
            $qb->andWhere('p.price <= :maxPrice')
               ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['bedrooms'])) {
            $qb->andWhere('p.bedrooms >= :bedrooms')
               ->setParameter('bedrooms', $filters['bedrooms']);
        }

        if (!empty($filters['capacity'])) {
            $qb->andWhere('p.capacity >= :capacity')
               ->setParameter('capacity', $filters['capacity']);
        }

        return $qb;
    }

    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->andWhere('LOWER(p.city) = LOWER(:city)')
            ->setParameter('status', Property::STATUS_PUBLISHED)
            ->setParameter('city', $city)
            ->getQuery()
            ->getResult();
    }

    public function getDistinctCities(): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('DISTINCT p.city')
            ->where('p.status = :status')
            ->setParameter('status', Property::STATUS_PUBLISHED)
            ->orderBy('p.city', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'city');
    }

    public function countProperties(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPublishedProperties(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.status = :status')
            ->setParameter('status', Property::STATUS_PUBLISHED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopRatedProperties(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->where('p.status = :status')
            ->andWhere('r.isApproved = :approved')
            ->setParameter('status', Property::STATUS_PUBLISHED)
            ->setParameter('approved', true)
            ->groupBy('p.id')
            ->orderBy('AVG(r.rating)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMostBookedProperties(int $limit = 5): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $limit = max(1, (int) $limit);
        
        $sql = "
            SELECT 
                p.id,
                p.title,
                p.city,
                p.main_image,
                COUNT(r.id) as reservations_count,
                COALESCE(SUM(pay.amount), 0) as total_revenue
            FROM properties p
            LEFT JOIN reservations r ON r.property_id = p.id AND r.status IN ('confirmed', 'completed')
            LEFT JOIN payments pay ON pay.reservation_id = r.id AND pay.status = 'completed'
            WHERE p.status = :status
            GROUP BY p.id, p.title, p.city, p.main_image
            ORDER BY reservations_count DESC
            LIMIT {$limit}
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'status' => Property::STATUS_PUBLISHED,
        ]);
        
        $rows = $result->fetchAllAssociative();
        
        return array_map(function($row) {
            return [
                'id' => $row['id'],
                'title' => $row['title'],
                'city' => $row['city'],
                'mainImage' => $row['main_image'],
                'reservationsCount' => (int) $row['reservations_count'],
                'totalRevenue' => (float) $row['total_revenue'],
            ];
        }, $rows);
    }
}
