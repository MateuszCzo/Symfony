<?php

namespace App\Form\Handler;

use Symfony\Component\Form\FormInterface;
use App\Entity\Discount;

class ProductsPercentageDiscountHandler extends DiscountFormHandlerTypeParent implements DiscountFormHandlerInterface
{
    public function handle(FormInterface $form, Discount $discount): Discount
    {
        /**  @var ArrayCollection $products */
        $products = $form->get('products')->getData();

        if (!$products->count()) return $discount;
        
        $discount = $this->removeDiscountProducts($discount);

        foreach($products as $product) $discount->addProduct($product);

        $discount->setCriteria([]);

        return $discount;
    }
}