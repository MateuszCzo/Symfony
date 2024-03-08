<?php

namespace App\DTO;

use App\Entity\Product;

interface PriceEnquiryInterface extends PromotionEnquiryInterface
{
    public function setPrice(int $price): self;

    public function getQuantity(): ?int;

    public function setDiscountedPrice(int $discountedPrice): self;
}