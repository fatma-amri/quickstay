<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Payment;
use App\Entity\Property;
use App\Entity\Reservation;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Créer les catégories
        $categories = [];
        $categoryData = [
            ['name' => 'Appartements', 'slug' => 'appartements', 'icon' => 'fa-building'],
            ['name' => 'Maisons', 'slug' => 'maisons', 'icon' => 'fa-home'],
            ['name' => 'Villas', 'slug' => 'villas', 'icon' => 'fa-hotel'],
            ['name' => 'Studios', 'slug' => 'studios', 'icon' => 'fa-door-open'],
        ];

        foreach ($categoryData as $data) {
            $category = new Category();
            $category->setName($data['name'])
                ->setSlug($data['slug'])
                ->setIcon($data['icon'])
                ->setIsActive(true);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Créer l'admin
        $admin = new User();
        $admin->setEmail('admin@quickstay.tn')
            ->setName('Administrateur')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'))
            ->setIsVerified(true);
        $manager->persist($admin);

        // Créer des utilisateurs
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@quickstay.tn")
                ->setName("Utilisateur {$i}")
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword($user, 'user123'))
                ->setIsVerified(true);
            $manager->persist($user);
            $users[] = $user;
        }

        // Créer des propriétés
        $properties = [];
        $propertyData = [
            ['title' => 'Appartement moderne à Tunis', 'city' => 'Tunis', 'price' => '150', 'image' => 'images/properties/apartment.png'],
            ['title' => 'Villa avec piscine à Hammamet', 'city' => 'Hammamet', 'price' => '350', 'image' => 'images/properties/villa.png'],
            ['title' => 'Studio cosy à Sousse', 'city' => 'Sousse', 'price' => '80', 'image' => 'images/properties/studio.png'],
            ['title' => 'Maison traditionnelle à Sidi Bou Said', 'city' => 'Sidi Bou Said', 'price' => '200', 'image' => 'images/properties/house.png'],
            ['title' => 'Loft design à La Marsa', 'city' => 'La Marsa', 'price' => '180', 'image' => 'images/properties/loft.png'],
        ];

        foreach ($propertyData as $index => $data) {
            $property = new Property();
            $property->setTitle($data['title'])
                ->setDescription('Description détaillée de la propriété. Logement confortable et bien équipé.')
                ->setPrice($data['price'])
                ->setAddress('123 Rue Exemple')
                ->setCity($data['city'])
                ->setCountry('Tunisie')
                ->setType(Property::TYPE_APARTMENT)
                ->setBedrooms(rand(1, 4))
                ->setBathrooms(rand(1, 2))
                ->setCapacity(rand(2, 8))
                ->setAmenities(['wifi', 'ac', 'kitchen'])
                ->setStatus(Property::STATUS_PUBLISHED)
                ->setIsAvailable(true)
                ->setOwner($admin)
                ->setCategory($categories[$index % count($categories)])
                ->setMainImage($data['image'])
                ->setImages([$data['image']]);
            $manager->persist($property);
            $properties[] = $property;
        }

        // Créer des réservations
        foreach ($users as $index => $user) {
            $property = $properties[$index % count($properties)];

            $reservation = new Reservation();
            $reservation->setUser($user)
                ->setProperty($property)
                ->setStartDate(new \DateTime('+' . ($index + 1) . ' weeks'))
                ->setEndDate(new \DateTime('+' . ($index + 2) . ' weeks'))
                ->setGuests(rand(1, 4))
                ->setStatus($index % 2 === 0 ? Reservation::STATUS_CONFIRMED : Reservation::STATUS_PENDING);
            $reservation->calculatePricing();
            $manager->persist($reservation);

            // Créer un paiement pour les réservations confirmées
            if ($reservation->isConfirmed()) {
                $payment = new Payment();
                $payment->setUser($user)
                    ->setReservation($reservation)
                    ->setAmount($reservation->getTotalPrice())
                    ->setStatus(Payment::STATUS_COMPLETED)
                    ->setMethod(Payment::METHOD_CARD)
                    ->setPaidAt(new \DateTime());
                $manager->persist($payment);

                // Créer un avis
                $review = new Review();
                $review->setAuthor($user)
                    ->setProperty($property)
                    ->setReservation($reservation)
                    ->setRating(rand(3, 5))
                    ->setComment('Excellent séjour ! Je recommande vivement.')
                    ->setIsApproved(true);
                $manager->persist($review);
            }
        }

        $manager->flush();
    }
}
