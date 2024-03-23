<?php

namespace App\Form;

use App\Constants\DeliveryConstants;
use App\Entity\Delivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter delivery name',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => true,
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter delivery description',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => DeliveryConstants::DELIVERY_TYPES,
                'required' => true,
                'label' => 'Type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter delivery description',
                    ]),
                ],
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Active',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
