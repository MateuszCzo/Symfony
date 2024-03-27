<?php

namespace App\Form;

use App\Entity\Attatchment;
use App\Entity\Category;
use App\Entity\Manufacturer;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Regex;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter product name',
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
                        'message' => 'Please enter product description',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'label' => 'Price',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter product price',
                    ]),
                    new Positive([
                        'message' => 'Please enter price greather than zero'
                    ]),
                    new Regex([
                        'pattern' => '/^\d+(\.\d{1,2})?$/',
                        'message' => 'Please enter a valid price'
                    ])
                ],
            ])
            ->add('quantity', NumberType::class, [
                'required' => true,
                'label' => 'Quantity',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter product quantity',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Please enter product price',
                    ])
                ],
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
                'label' => 'Active',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
                'required' => true,
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Image',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter product image',
                    ]),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ]),
                ],
            ])
            ->add('images', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'label' => 'Other Images',
                'constraints' => [
                    /* todo file validation
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ]),
                    */
                ],
            ])
            ->add('subcategories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
                'label' => 'Subcategories',
            ])
            ->add('attatchments', EntityType::class, [
                'class' => Attatchment::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
                'label' => 'Attatchments',
            ])
            ->add('manufacturer', EntityType::class, [
                'class' => Manufacturer::class,
                'choice_label' => 'id',
                'required' => true,
                'label' => 'Manufacturer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
