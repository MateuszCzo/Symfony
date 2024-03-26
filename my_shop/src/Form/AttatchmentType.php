<?php

namespace App\Form;

use App\Entity\Attatchment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AttatchmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fileName', TextType::class, [
                'required' => true,
                'label' => 'File Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter attatchment file name',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter attatchment description',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'File',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter attatchment file',
                    ]),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid file',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attatchment::class,
        ]);
    }
}
