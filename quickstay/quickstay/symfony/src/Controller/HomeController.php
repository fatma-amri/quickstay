<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        PropertyRepository $propertyRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $featuredProperties = $propertyRepository->findFeatured(6);
        $latestProperties = $propertyRepository->findPublishedQueryBuilder()
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
        $categories = $categoryRepository->findActive();
        $cities = $propertyRepository->getDistinctCities();

        return $this->render('home/index.html.twig', [
            'featuredProperties' => $featuredProperties,
            'latestProperties' => $latestProperties,
            'categories' => $categories,
            'cities' => array_slice($cities, 0, 6),
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}
