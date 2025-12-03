<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/properties')]
#[IsGranted('ROLE_ADMIN')]
class PropertyController extends AbstractController
{
    #[Route('', name: 'admin_properties')]
    public function index(
        PropertyRepository $propertyRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $queryBuilder = $propertyRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/property/index.html.twig', [
            'properties' => $pagination,
        ]);
    }

    #[Route('/new', name: 'admin_property_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'image principale
            $mainImageFile = $form->get('mainImageFile')->getData();
            if ($mainImageFile) {
                $mainImageFilename = $fileUploader->upload($mainImageFile);
                $property->setMainImage($mainImageFilename);
            }

            // Gérer les images supplémentaires
            $imageFiles = $form->get('imageFiles')->getData();
            if ($imageFiles) {
                $images = [];
                foreach ($imageFiles as $imageFile) {
                    $images[] = $fileUploader->upload($imageFile);
                }
                $property->setImages($images);
            }

            $property->setOwner($this->getUser());

            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'La propriété a été créée avec succès.');
            return $this->redirectToRoute('admin_properties');
        }

        return $this->render('admin/property/new.html.twig', [
            'form' => $form->createView(),
            'property' => $property,
        ]);
    }

    #[Route('/{id}', name: 'admin_property_show', requirements: ['id' => '\d+'])]
    public function show(Property $property): Response
    {
        return $this->render('admin/property/show.html.twig', [
            'property' => $property,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_property_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Property $property,
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'image principale
            $mainImageFile = $form->get('mainImageFile')->getData();
            if ($mainImageFile) {
                // Supprimer l'ancienne image
                if ($property->getMainImage()) {
                    $fileUploader->remove($property->getMainImage());
                }
                $mainImageFilename = $fileUploader->upload($mainImageFile);
                $property->setMainImage($mainImageFilename);
            }

            // Gérer les nouvelles images
            $imageFiles = $form->get('imageFiles')->getData();
            if ($imageFiles) {
                $images = $property->getImages();
                foreach ($imageFiles as $imageFile) {
                    $images[] = $fileUploader->upload($imageFile);
                }
                $property->setImages($images);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La propriété a été modifiée avec succès.');
            return $this->redirectToRoute('admin_properties');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_property_delete', methods: ['POST'])]
    public function delete(
        Property $property,
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
            // Supprimer les images
            if ($property->getMainImage()) {
                $fileUploader->remove($property->getMainImage());
            }
            foreach ($property->getImages() as $image) {
                $fileUploader->remove($image);
            }

            $entityManager->remove($property);
            $entityManager->flush();

            $this->addFlash('success', 'La propriété a été supprimée.');
        }

        return $this->redirectToRoute('admin_properties');
    }

    #[Route('/{id}/toggle-featured', name: 'admin_property_toggle_featured', methods: ['POST'])]
    public function toggleFeatured(
        Property $property,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('toggle' . $property->getId(), $request->request->get('_token'))) {
            $property->setIsFeatured(!$property->isFeatured());
            $entityManager->flush();

            $this->addFlash('success', $property->isFeatured() 
                ? 'La propriété est maintenant mise en avant.' 
                : 'La propriété n\'est plus mise en avant.');
        }

        return $this->redirectToRoute('admin_properties');
    }
}
