<?php

namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Entity\Promotion;
use App\Filter\LowestPriceFilter;
use App\Tests\ServiceTestCase;

class LowestPriceFilterTest extends ServiceTestCase
{
    /** @test */
    public function lowestPricePromotionsFilteringIsAppliedCorectly(): void
    {
        // Given
        $lowestPriceFilter = $this->container->get(LowestPriceFilter::class);
        $enquiry = new LowestPriceEnquiry();
        $promotions = $this->promotionsDataProvider();

        // When
        $filteredEnquiry = $lowestPriceFilter->apply($enquiry, ...$promotions);

        // Then
        $this->assertSame(100, $filteredEnquiry->getPrice());
        $this->assertSame(50, $filteredEnquiry->getDiscountedPrice());
        $this->assertSame('Black Friday half price sale', $filteredEnquiry->getPromotionName());
    }

    public function promotionsDataProvider(): array
    {
        $promotion1 = new Promotion();
        $promotion1->setName('Black Friday half price sale');
        $promotion1->setAdjustment(0.5);
        $promotion1->setCriteria(['from' => '2022-11-25', 'to' => '2022-11-28']);
        $promotion1->setName('date_range_multiplier');

        $promotion2 = new Promotion();
        $promotion2->setName('Voucher OU812');
        $promotion2->setAdjustment(100);
        $promotion2->setCriteria(['code' => 'OU812']);
        $promotion2->setName('fixed_price_voucher');

        $promotion3 = new Promotion();
        $promotion3->setName('Buy one get one free');
        $promotion3->setAdjustment(0.5);
        $promotion3->setCriteria(['minimum_quantity' => 2]);
        $promotion3->setName('event_item__multiplier');

        return [$promotion1, $promotion2, $promotion3];
    }
}