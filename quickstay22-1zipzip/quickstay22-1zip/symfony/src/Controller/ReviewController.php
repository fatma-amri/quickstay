<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Reservation;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reviews')]
#[IsGranted('ROLE_USER')]
class ReviewController extends AbstractController
{
    #[Route('/new/{reservationId}', name: 'app_review_new', requirements: ['reservationId' => '\d+'])]
    public function new(
        int $reservationId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas laisser un avis pour cette réservation.');
        }

        if (!$reservation->isCompleted()) {
            $this->addFlash('error', 'Vous ne pouvez laisser un avis qu\'après votre séjour.');
            return $this->redirectToRoute('app_reservations');
        }

        if ($reservation->hasReview()) {
            $this->addFlash('warning', 'Vous avez déjà laissé un avis pour cette réservation.');
            return $this->redirectToRoute('app_reservations');
        }

        $review = new Review();
        $review->setAuthor($this->getUser());
        $review->setProperty($reservation->getProperty());
        $review->setReservation($reservation);

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre avis! Il sera publié après validation.');
            return $this->redirectToRoute('app_reservations');
        }

        return $this->render('review/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
}
