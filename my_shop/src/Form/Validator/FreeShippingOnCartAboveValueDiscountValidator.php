<?php

namespace App\Form\Validator;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FreeShippingOnCartAboveValueDiscountValidator implements DiscountTypeValidatorInterface
{
    public function validate(FormInterface $form, int $action): bool
    {
        $cartValue = $form->get('cartValue')->getData();

        if (!is_numeric($cartValue)) {
            $form->addError(new FormError('Enter discount value'));
            return false;
        }

        return true;
    }
}