<?php

namespace App\Form\Validator;

use App\Constants\DiscountConstants;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class CategoriesPercentageDiscountValidator implements DiscountTypeValidatorInterface
{
    public function validate(FormInterface $form, int $action): bool
    {
        if ($action == DiscountConstants::ACTION_UPDATE) return true;

        /** @var ArrayCollection $categories */
        $categories = $form->get('categories')->getData();

        if (!$categories->count()) {
            $form->addError(new FormError('Select category'));
            return false;
        }

        return true;
    }
}