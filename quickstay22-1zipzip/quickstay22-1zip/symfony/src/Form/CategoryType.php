<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom de la catégorie']
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'nom-de-la-categorie']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('icon', TextType::class, [
                'label' => 'Icône (Font Awesome)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'fa-solid fa-house']
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur',
                'required' => false,
                'attr' => ['class' => 'form-control form-control-color']
            ])
            ->add('sortOrder', IntegerType::class, [
                'label' => 'Ordre d\'affichage',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Catégorie active',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'category_form',
        ]);
    }
}
