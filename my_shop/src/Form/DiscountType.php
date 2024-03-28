<?php

namespace App\Form;

use App\Constants\DiscountConstants;
use App\Entity\Discount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter discount name'
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('value', NumberType::class, [
                'required' => true,
                'label' => 'Value',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter discount value',
                    ]),
                    new Positive([
                        'message' => 'Please enter discount value greather than zero'
                    ])
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => DiscountConstants::TYPES,
                'required' => true,
                'label' => 'Type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter discount type',
                    ]),
                ],
            ])
        ;

        foreach(DiscountConstants::FORM_FIELDS as $formField) {
            $builder->add(...$formField);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discount::class,
        ]);
    }
}
