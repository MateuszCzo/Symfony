<?php

namespace App\DTO;

use App\Entity\Product;
use JsonSerializable;

interface PromotionEnquiryInterface extends JsonSerializable
{
    public function getProduct(): ?Product;

    public function setPromotionId(int $promotionId): self;

    public function setPromotionName(string $promotionName): self;
}