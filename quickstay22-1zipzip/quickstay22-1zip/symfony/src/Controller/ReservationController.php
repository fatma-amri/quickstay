<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Property;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservations')]
#[IsGranted('ROLE_USER')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'app_reservations')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $reservations = $reservationRepository->findByUserWithProperty($user->getId());

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new/{id}', name: 'app_reservation_new', requirements: ['id' => '\d+'])]
    public function new(
        Property $property,
        Request $request,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository
    ): Response {
        if (!$property->isPublished() || !$property->isAvailable()) {
            $this->addFlash('error', 'Cette propriété n\'est pas disponible à la réservation.');
            return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
        }

        $reservation = new Reservation();
        $reservation->setProperty($property);
        $reservation->setUser($this->getUser());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier la disponibilité
            $isAvailable = $reservationRepository->checkAvailability(
                $property->getId(),
                $reservation->getStartDate(),
                $reservation->getEndDate()
            );

            if (!$isAvailable) {
                $this->addFlash('error', 'Ces dates ne sont pas disponibles.');
                return $this->redirectToRoute('app_reservation_new', ['id' => $property->getId()]);
            }

            // Vérifier la capacité
            if ($reservation->getGuests() > $property->getCapacity()) {
                $this->addFlash('error', 'Le nombre de personnes dépasse la capacité du logement.');
                return $this->redirectToRoute('app_reservation_new', ['id' => $property->getId()]);
            }

            $reservation->calculatePricing();
            
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de réservation a été envoyée avec succès!');
            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/new.html.twig', [
            'property' => $property,
            'reservationForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', requirements: ['id' => '\d+'])]
    public function show(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('RESERVATION_VIEW', $reservation);

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_reservation_cancel', methods: ['POST'])]
    public function cancel(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('RESERVATION_CANCEL', $reservation);

        if ($this->isCsrfTokenValid('cancel' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->cancel('Annulée par le client');
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été annulée.');
        }

        return $this->redirectToRoute('app_reservations');
    }
}
