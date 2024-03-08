<?php

namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Entity\Promotion;
use App\Filter\Modifier\DateRangeMultiplier;
use App\Filter\Modifier\EvenItemsMultiplier;
use App\Filter\Modifier\FixedPriceVoucher;
use App\Tests\ServiceTestCase;

class PriceModifiersTest extends ServiceTestCase
{
    /** @test */
    public function DateRangeMultiplierReturnACorrectlyModifiedPrice(): void
    {
        // Given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setRequestDate('2022-11-27');
        $promotion = new Promotion();
        $promotion->setName('Black Friday half price sale');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(['from' => '2022-11-25', 'to' => '2022-11-28']);
        $promotion->setType ('date_range_multiplier');
        $dateRangeModifier = new DateRangeMultiplier();

        // When
        $modifiedPrice = $dateRangeModifier->modify(100, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(250, $modifiedPrice);
    }

    /** @test */
    public function FixedPriceVoucherReturnsACorectlyModifiedPrice(): void
    {
        // Given
        $fixedPriceVoucher = new FixedPriceVoucher();
        $promotion = new Promotion();
        $promotion->setName('Voucher OU812');
        $promotion->setAdjustment(100);
        $promotion->setCriteria(['code' => 'OU812']);
        $promotion->setType('fixed_price_voucher');
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode('OU812');

        // When
        $modifiedPrice = $fixedPriceVoucher->modify(150, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(500, $modifiedPrice);
    }

    /** @test */
    public function EvenItemsModifierReturnsACorectlyModifiedPrice(): void
    {
        // Given
        $evenItemsModifier = new EvenItemsMultiplier();
        $promotion = new Promotion();
        $promotion->setName('Buy one get one free');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(['minimum_quantity' => 2]);
        $promotion->setType('even_item_multiplier');
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode('OU812');

        // When
        $modifiedPrice = $evenItemsModifier->modify(100, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(300, $modifiedPrice);
    }

    /** @test */
    public function EvenItemsModifierReturnsACorectlyModifiedPriceAlternative(): void
    {
        // Given
        $evenItemsModifier = new EvenItemsMultiplier();
        $promotion = new Promotion();
        $promotion->setName('Buy one get one half price');
        $promotion->setAdjustment(0.75);
        $promotion->setCriteria(['minimum_quantity' => 2]);
        $promotion->setType('even_item_multiplier');
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode('OU812');

        // When
        $modifiedPrice = $evenItemsModifier->modify(100, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(400, $modifiedPrice);
    }
}