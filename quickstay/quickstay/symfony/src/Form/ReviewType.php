<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ratingChoices = [
            '1 - Très mauvais' => 1,
            '2 - Mauvais' => 2,
            '3 - Moyen' => 3,
            '4 - Bon' => 4,
            '5 - Excellent' => 5,
        ];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'avis',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Résumez votre expérience'
                ]
            ])
            ->add('rating', HiddenType::class, [
                'attr' => ['class' => 'rating-value']
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Partagez votre expérience...'
                ]
            ])
            ->add('cleanlinessRating', ChoiceType::class, [
                'label' => 'Propreté',
                'choices' => $ratingChoices,
                'required' => false,
                'placeholder' => 'Sélectionnez',
                'attr' => ['class' => 'form-select']
            ])
            ->add('communicationRating', ChoiceType::class, [
                'label' => 'Communication',
                'choices' => $ratingChoices,
                'required' => false,
                'placeholder' => 'Sélectionnez',
                'attr' => ['class' => 'form-select']
            ])
            ->add('locationRating', ChoiceType::class, [
                'label' => 'Emplacement',
                'choices' => $ratingChoices,
                'required' => false,
                'placeholder' => 'Sélectionnez',
                'attr' => ['class' => 'form-select']
            ])
            ->add('valueRating', ChoiceType::class, [
                'label' => 'Rapport qualité/prix',
                'choices' => $ratingChoices,
                'required' => false,
                'placeholder' => 'Sélectionnez',
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'review_form',
        ]);
    }
}
