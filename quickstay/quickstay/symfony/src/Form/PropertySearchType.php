<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Où souhaitez-vous aller?'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de logement',
                'required' => false,
                'placeholder' => 'Tous les types',
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
                'placeholder' => 'Toutes les catégories',
                'attr' => ['class' => 'form-select']
            ])
            ->add('minPrice', MoneyType::class, [
                'label' => 'Prix minimum',
                'currency' => 'TND',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Min']
            ])
            ->add('maxPrice', MoneyType::class, [
                'label' => 'Prix maximum',
                'currency' => 'TND',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Max']
            ])
            ->add('bedrooms', IntegerType::class, [
                'label' => 'Chambres minimum',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Personnes',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 1]
            ])
            ->add('checkIn', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('checkOut', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
