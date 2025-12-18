<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d')
                ]
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('guests', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => $options['max_guests'] ?? 10
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message au propriétaire (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Présentez-vous et expliquez le but de votre séjour...'
                ]
            ])
            ->add('specialRequests', TextareaType::class, [
                'label' => 'Demandes spéciales (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Arrivée tardive, lit bébé, etc.'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'reservation_form',
            'max_guests' => 10,
        ]);
    }
}
