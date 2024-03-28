<?php

namespace App\Form\Validator;

use Symfony\Component\Form\FormInterface;

interface DiscountTypeValidatorInterface
{
    public function validate(FormInterface $form, int $action): bool;
}