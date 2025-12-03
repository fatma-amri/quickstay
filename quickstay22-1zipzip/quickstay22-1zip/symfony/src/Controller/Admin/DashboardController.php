<?php

namespace App\Controller\Admin;

use App\Repository\PaymentRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private PropertyRepository $propertyRepository,
        private UserRepository $userRepository,
        private PaymentRepository $paymentRepository,
        private ReviewRepository $reviewRepository
    ) {}

    #[Route('', name: 'admin_dashboard')]
    public function index(): Response
    {
        // Statistiques générales
        $stats = [
            'totalReservations' => $this->reservationRepository->count([]),
            'pendingReservations' => $this->reservationRepository->count(['status' => 'pending']),
            'confirmedReservations' => $this->reservationRepository->count(['status' => 'confirmed']),
            'totalProperties' => $this->propertyRepository->count([]),
            'publishedProperties' => $this->propertyRepository->countPublishedProperties(),
            'totalUsers' => $this->userRepository->countUsers(),
            'newUsersThisMonth' => $this->userRepository->countNewUsersThisMonth(),
            'totalRevenue' => $this->paymentRepository->getTotalRevenue(),
            'monthlyRevenue' => $this->paymentRepository->getRevenueThisMonth(),
            'pendingReviews' => $this->reviewRepository->countPendingReviews(),
        ];

        // Taux d'occupation
        $stats['occupancyRate'] = $this->calculateOccupancyRate();

        // Données pour les graphiques
        $chartData = [
            'reservationsByMonth' => $this->reservationRepository->getReservationsByMonth(),
            'revenueByMonth' => $this->paymentRepository->getRevenueByMonth(),
            'reservationsByStatus' => $this->reservationRepository->getReservationsByStatus(),
        ];

        // Top propriétés
        $topProperties = $this->propertyRepository->getMostBookedProperties(5);

        // Dernières réservations
        $latestReservations = $this->reservationRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            10
        );

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'chartData' => $chartData,
            'topProperties' => $topProperties,
            'latestReservations' => $latestReservations,
        ]);
    }

    #[Route('/stats/json', name: 'admin_stats_json')]
    public function statsJson(): Response
    {
        $data = [
            'reservationsByMonth' => $this->reservationRepository->getReservationsByMonth(),
            'revenueByMonth' => $this->paymentRepository->getRevenueByMonth(),
            'reservationsByStatus' => $this->reservationRepository->getReservationsByStatus(),
        ];

        return $this->json($data);
    }

    private function calculateOccupancyRate(): float
    {
        $totalProperties = $this->propertyRepository->countPublishedProperties();
        if ($totalProperties === 0) {
            return 0;
        }

        $occupiedProperties = $this->reservationRepository->countCurrentlyOccupiedProperties();
        return round(($occupiedProperties / $totalProperties) * 100, 1);
    }
}
