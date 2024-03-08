<?php

namespace App\Event;

use App\DTO\PromotionEnquiryInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AfterDTOCreatedEvant extends Event
{
    public const NAME ='dto.created';

    protected $dto;

    public function __construct(PromotionEnquiryInterface $dto) {
        $this->dto = $dto;
    }

    public function getDTO(): PromotionEnquiryInterface {
        return $this->dto;
    }
}