<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/reviews')]
#[IsGranted('ROLE_ADMIN')]
class ReviewController extends AbstractController
{
    #[Route('', name: 'admin_reviews')]
    public function index(
        ReviewRepository $reviewRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $filter = $request->query->get('filter', 'pending');

        $queryBuilder = $reviewRepository->createQueryBuilder('r')
            ->leftJoin('r.author', 'u')
            ->leftJoin('r.property', 'p')
            ->addSelect('u', 'p')
            ->orderBy('r.createdAt', 'DESC');

        if ($filter === 'pending') {
            $queryBuilder->where('r.isApproved = :approved')
                ->setParameter('approved', false);
        } elseif ($filter === 'approved') {
            $queryBuilder->where('r.isApproved = :approved')
                ->setParameter('approved', true);
        } elseif ($filter === 'reported') {
            $queryBuilder->where('r.isReported = :reported')
                ->setParameter('reported', true);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/review/index.html.twig', [
            'reviews' => $pagination,
            'currentFilter' => $filter,
        ]);
    }

    #[Route('/{id}', name: 'admin_review_show', requirements: ['id' => '\d+'])]
    public function show(Review $review): Response
    {
        return $this->render('admin/review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/approve', name: 'admin_review_approve', methods: ['POST'])]
    public function approve(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('approve' . $review->getId(), $request->request->get('_token'))) {
            $review->approve();
            $review->setIsReported(false);
            $entityManager->flush();

            $this->addFlash('success', 'L\'avis a été approuvé.');
        }

        return $this->redirectToRoute('admin_reviews');
    }

    #[Route('/{id}/reject', name: 'admin_review_reject', methods: ['POST'])]
    public function reject(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('reject' . $review->getId(), $request->request->get('_token'))) {
            $review->reject();
            $entityManager->flush();

            $this->addFlash('success', 'L\'avis a été rejeté.');
        }

        return $this->redirectToRoute('admin_reviews');
    }

    #[Route('/{id}/delete', name: 'admin_review_delete', methods: ['POST'])]
    public function delete(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();

            $this->addFlash('success', 'L\'avis a été supprimé.');
        }

        return $this->redirectToRoute('admin_reviews');
    }
}
