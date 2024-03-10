<?php

namespace App\Tests;

use App\Entity\Stock;

/**
 * Stock Repository test
 */
class StockTest extends DatabaseDependantTestCase
{
    /** @test */
    public function a_stock_record_can_be_created_in_database()
    {   
        // Set up
        $stock = new Stock();
        $stock->setSymbol('AMZN');
        $stock->setShortName('Amazon.com Inc.');
        $stock->setCurrency('USD');
        $stock->setExchangeName('NasdaqGS');
        $stock->setRegion('US');
        $price = 1000;
        $previousClose = 1100;
        $priceChange = $price - $previousClose;
        $stock->setPrice($price);
        $stock->setPreviousClose($previousClose);
        $stock->setPriceChange($priceChange);

        $this->entityManager->persist($stock);

        // Do something
        $this->entityManager->flush();
        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stockRecord = $stockRepository->findOneBy(['symbol' => 'AMZN']);

        // Make assertions
        $this->assertEquals('Amazon.com Inc.', $stockRecord->getShortName());
        $this->assertEquals('USD', $stockRecord->getCurrency());
        $this->assertEquals('NasdaqGS', $stockRecord->getExchangeName());
        $this->assertEquals('US', $stockRecord->getRegion());
        $this->assertEquals(1000, $stockRecord->getPrice());
        $this->assertEquals(1100, $stockRecord->getPreviousClose());
        $this->assertEquals(-100, $stockRecord->getPriceChange());
    }
}