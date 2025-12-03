<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\Reservation;
use App\Form\PropertySearchType;
use App\Form\ReservationType;
use App\Repository\PropertyRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/properties')]
class PropertyController extends AbstractController
{
    #[Route('', name: 'app_properties')]
    public function index(
        Request $request,
        PropertyRepository $propertyRepository,
        PaginatorInterface $paginator
    ): Response {
        $searchForm = $this->createForm(PropertySearchType::class);
        $searchForm->handleRequest($request);

        $filters = [];
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $filters = array_filter($searchForm->getData());
        }

        $queryBuilder = $propertyRepository->searchProperties($filters);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('property/index.html.twig', [
            'properties' => $pagination,
            'searchForm' => $searchForm,
        ]);
    }

    #[Route('/{id}', name: 'app_property_show', requirements: ['id' => '\d+'])]
    public function show(
        Property $property,
        ReviewRepository $reviewRepository,
        ReservationRepository $reservationRepository,
        Request $request
    ): Response {
        if (!$property->isPublished() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException('Propriété non trouvée');
        }

        $reviews = $reviewRepository->findApprovedByProperty($property->getId());
        $averageRating = $reviewRepository->getAverageRatingByProperty($property->getId());

        // Formulaire de réservation
        $reservation = new Reservation();
        $reservation->setProperty($property);
        $reservationForm = $this->createForm(ReservationType::class, $reservation);

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reservationForm' => $reservationForm,
        ]);
    }

    #[Route('/city/{city}', name: 'app_properties_by_city')]
    public function byCity(
        string $city,
        PropertyRepository $propertyRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $queryBuilder = $propertyRepository->searchProperties(['city' => $city]);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('property/index.html.twig', [
            'properties' => $pagination,
            'city' => $city,
            'searchForm' => $this->createForm(PropertySearchType::class),
        ]);
    }
}
