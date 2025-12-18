<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAdmins(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
    }

    public function countUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNewUsersThisMonth(): int
    {
        $startOfMonth = new \DateTime('first day of this month');
        
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt >= :start')
            ->setParameter('start', $startOfMonth)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getUsersRegistrationByMonth(int $months = 12): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $months = max(1, (int) $months);
        
        $sql = "
            SELECT 
                TO_CHAR(DATE_TRUNC('month', created_at), 'YYYY-MM') as month,
                COUNT(*) as count
            FROM users
            WHERE created_at >= NOW() - MAKE_INTERVAL(months => {$months})
            GROUP BY DATE_TRUNC('month', created_at)
            ORDER BY month ASC
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        
        return $result->fetchAllAssociative();
    }
}
