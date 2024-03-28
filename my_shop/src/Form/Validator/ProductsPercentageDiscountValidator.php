<?php

namespace App\Form\Validator;

use App\Constants\DiscountConstants;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class ProductsPercentageDiscountValidator implements DiscountTypeValidatorInterface
{
    public function validate(FormInterface $form, int $action): bool
    {
        if ($action == DiscountConstants::ACTION_UPDATE) return true;

        /** @var ArrayCollection $products */
        $products = $form->get('products')->getData();

        if (!$products->count()) {
            $form->addError(new FormError('Select product'));
            return false;
        }

        return true;
    }
}