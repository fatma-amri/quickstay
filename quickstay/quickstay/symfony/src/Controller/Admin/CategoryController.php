<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'admin_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['sortOrder' => 'ASC', 'name' => 'ASC']);

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'admin_category_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($category->getSlug())) {
                $category->setSlug(strtolower($slugger->slug($category->getName())));
            }

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Category $category,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($category->getSlug())) {
                $category->setSlug(strtolower($slugger->slug($category->getName())));
            }

            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(
        Category $category,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            if ($category->getProperties()->count() > 0) {
                $this->addFlash('error', 'Cette catégorie contient des propriétés et ne peut pas être supprimée.');
                return $this->redirectToRoute('admin_categories');
            }

            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été supprimée.');
        }

        return $this->redirectToRoute('admin_categories');
    }
}
