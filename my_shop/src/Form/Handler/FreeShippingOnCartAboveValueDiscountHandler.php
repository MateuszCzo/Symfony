<?php

namespace App\Form\Handler;

use Symfony\Component\Form\FormInterface;
use App\Entity\Discount;

class FreeShippingOnCartAboveValueDiscountHandler extends DiscountFormHandlerTypeParent implements DiscountFormHandlerInterface
{
    public function handle(FormInterface $form, Discount $discount): Discount
    {
        return $discount->setCriteria([
            'cartValue' => $form->get('cartValue')->getData(),
        ]);
    }
}