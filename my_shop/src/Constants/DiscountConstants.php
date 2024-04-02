<?php

namespace App\Constants;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DiscountConstants
{
    public const ACTION_CREATE = 1;
    public const ACTION_UPDATE = 2;

    public const TYPES = [
        'Categories Percentage Discount' => 'categories_percentage_discount',
        'Products Percentage Discount' => 'products_percentage_discount',
        'Free Shipping On Cart Above Value' => 'free_shipping_on_cart_above_value_discount'
    ];

    public const FORM_FIELDS = [
        'categories_percentage_discount' => [
            'categories',
            EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => 'id',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'label' => 'Categories',
                'attr' => ['class' => 'categories_percentage_discount discount-type'],
            ]
        ],
        'products_percentage_discount' => [
            'products',
            EntityType::class,
            [
                'class' => Product::class,
                'choice_label' => 'id',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'label' => 'Products',
                'attr' => ['class' => 'products_percentage_discount discount-type'],
            ]
        ],
        'free_shipping_on_cart_above_value_discount' => [
            'cartValue',
            NumberType::class,
            [
                'required' => false,
                'label' => 'Cart Value',
                'mapped' => false,
                'attr' => ['class' => 'free_shipping_on_cart_above_value_discount discount-type'],
            ]
        ]
    ];
}