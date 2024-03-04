<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border-b-2 w-fill h-20 text-6xl outline-none',
                    'placeholder' => 'Enter title...',
                ],
                'label' => false,
            ])
            ->add('releaseYear', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-transparent block mt-10 border-b-2 w-fill h-20 text-6xl outline-none',
                    'placeholder' => 'Enter release year...',
                ],
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'bg-transparent block mt-10 border-b-2 w-fill h-60 text-6xl outline-none',
                    'placeholder' => 'Enter description...',
                ],
                'label' => false,
            ])
            ->add('imagePath', FileType::class, [
                'required' => false,
                'mapped' => false,
                ])
            /*
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
