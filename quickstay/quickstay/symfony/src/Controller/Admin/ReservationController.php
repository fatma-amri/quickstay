<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reservations')]
#[IsGranted('ROLE_ADMIN')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'admin_reservations')]
    public function index(
        ReservationRepository $reservationRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $status = $request->query->get('status');

        $queryBuilder = $reservationRepository->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.property', 'p')
            ->addSelect('u', 'p')
            ->orderBy('r.createdAt', 'DESC');

        if ($status) {
            $queryBuilder->where('r.status = :status')
                ->setParameter('status', $status);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $pagination,
            'currentStatus' => $status,
        ]);
    }

    #[Route('/{id}', name: 'admin_reservation_show', requirements: ['id' => '\d+'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/confirm', name: 'admin_reservation_confirm', methods: ['POST'])]
    public function confirm(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('confirm' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->confirm();
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été confirmée.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}/reject', name: 'admin_reservation_reject', methods: ['POST'])]
    public function reject(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('reject' . $reservation->getId(), $request->request->get('_token'))) {
            $reason = $request->request->get('reason', 'Rejetée par l\'administrateur');
            $reservation->reject($reason);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été rejetée.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}/cancel', name: 'admin_reservation_cancel', methods: ['POST'])]
    public function cancel(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('cancel' . $reservation->getId(), $request->request->get('_token'))) {
            $reason = $request->request->get('reason', 'Annulée par l\'administrateur');
            $reservation->cancel($reason);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été annulée.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}/complete', name: 'admin_reservation_complete', methods: ['POST'])]
    public function complete(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('complete' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->complete();
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été marquée comme terminée.');
        }

        return $this->redirectToRoute('admin_reservation_show', ['id' => $reservation->getId()]);
    }

    #[Route('/{id}/delete', name: 'admin_reservation_delete', methods: ['POST'])]
    public function delete(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été supprimée.');
        }

        return $this->redirectToRoute('admin_reservations');
    }
}
