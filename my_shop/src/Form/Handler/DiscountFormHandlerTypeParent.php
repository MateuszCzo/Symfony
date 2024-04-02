<?php

namespace App\Form\Handler;

use App\Entity\Discount;
use App\Repository\ProductRepository;

class DiscountFormHandlerTypeParent
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    protected function removeDiscountProducts(Discount $discount): Discount
    {
        $oldProducts = $discount->getProducts();

        foreach($oldProducts as $oldProduct) {
            $discount->removeProduct($oldProduct);
        }

        return $discount;
    }
}