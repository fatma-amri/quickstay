<?php

namespace App\Service;

use App\Repository\PaymentRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;

class StatisticsService
{
    public function __construct(
        private UserRepository $userRepository,
        private PropertyRepository $propertyRepository,
        private ReservationRepository $reservationRepository,
        private PaymentRepository $paymentRepository,
        private ReviewRepository $reviewRepository
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'users' => [
                'total' => $this->userRepository->countUsers(),
                'newThisMonth' => $this->userRepository->countNewUsersThisMonth(),
            ],
            'properties' => [
                'total' => $this->propertyRepository->countProperties(),
                'published' => $this->propertyRepository->countPublishedProperties(),
            ],
            'reservations' => [
                'byStatus' => $this->reservationRepository->getReservationsByStatus(),
                'currentlyOccupied' => $this->reservationRepository->countCurrentlyOccupiedProperties(),
            ],
            'revenue' => [
                'total' => $this->paymentRepository->getTotalRevenue(),
                'thisMonth' => $this->paymentRepository->getRevenueThisMonth(),
            ],
            'reviews' => [
                'total' => $this->reviewRepository->countReviews(),
                'pending' => $this->reviewRepository->countPendingReviews(),
            ],
        ];
    }

    public function getChartData(): array
    {
        return [
            'reservationsByMonth' => $this->reservationRepository->getReservationsByMonth(12),
            'userRegistrations' => $this->userRepository->getUsersRegistrationByMonth(12),
            'revenueByMonth' => $this->paymentRepository->getRevenueByMonth(12),
        ];
    }

    public function getTopProperties(int $limit = 5): array
    {
        return [
            'topRated' => $this->propertyRepository->getTopRatedProperties($limit),
            'mostBooked' => $this->propertyRepository->getMostBookedProperties($limit),
        ];
    }

    public function getOccupancyRate(): float
    {
        $totalProperties = $this->propertyRepository->countPublishedProperties();
        if ($totalProperties === 0) {
            return 0;
        }

        $occupiedProperties = $this->reservationRepository->countCurrentlyOccupiedProperties();
        return round(($occupiedProperties / $totalProperties) * 100, 1);
    }
}
