<?php

namespace App\Form\Handler;

use App\Entity\Discount;
use Symfony\Component\Form\FormInterface;

interface DiscountFormHandlerInterface
{
    public function handle(FormInterface $form, Discount $discount): Discount;
}