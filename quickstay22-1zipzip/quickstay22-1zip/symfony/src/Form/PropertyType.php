<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Ex: Appartement moderne au centre-ville',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Décrivez votre propriété en détail...',
                    'class' => 'form-control'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix par nuit',
                'currency' => 'TND',
                'attr' => ['class' => 'form-control']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de logement',
                'choices' => [
                    'Appartement' => Property::TYPE_APARTMENT,
                    'Maison' => Property::TYPE_HOUSE,
                    'Villa' => Property::TYPE_VILLA,
                    'Studio' => Property::TYPE_STUDIO,
                    'Loft' => Property::TYPE_LOFT,
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => ['class' => 'form-select']
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Numéro et rue',
                    'class' => 'form-control'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ex: Tunis',
                    'class' => 'form-control'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('bedrooms', IntegerType::class, [
                'label' => 'Chambres',
                'attr' => ['min' => 0, 'class' => 'form-control']
            ])
            ->add('bathrooms', IntegerType::class, [
                'label' => 'Salles de bain',
                'attr' => ['min' => 0, 'class' => 'form-control']
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacité (personnes)',
                'attr' => ['min' => 1, 'class' => 'form-control']
            ])
            ->add('surface', TextType::class, [
                'label' => 'Surface (m²)',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('amenities', ChoiceType::class, [
                'label' => 'Équipements',
                'choices' => [
                    'WiFi' => 'wifi',
                    'Climatisation' => 'ac',
                    'Chauffage' => 'heating',
                    'Cuisine équipée' => 'kitchen',
                    'Lave-linge' => 'washer',
                    'Parking' => 'parking',
                    'Piscine' => 'pool',
                    'Jardin' => 'garden',
                    'Terrasse' => 'terrace',
                    'TV' => 'tv',
                    'Coffre-fort' => 'safe',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('mainImageFile', FileType::class, [
                'label' => 'Image principale',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, WebP)',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('imageFiles', FileType::class, [
                'label' => 'Images supplémentaires',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        ])
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Brouillon' => Property::STATUS_DRAFT,
                    'Publié' => Property::STATUS_PUBLISHED,
                    'Archivé' => Property::STATUS_ARCHIVED,
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('isAvailable', CheckboxType::class, [
                'label' => 'Disponible à la réservation',
                'required' => false,
            ])
            ->add('isFeatured', CheckboxType::class, [
                'label' => 'Mettre en avant',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'property_form',
        ]);
    }
}
