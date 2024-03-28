<?php

namespace App\Form\Handler;

use App\Repository\ProductRepository;

class DiscountFormHandlerTypeParent
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }
}