<?php

namespace App\Tests\feature;

use App\Entity\Stock;
use App\Http\FakeYahooFinanceApiClient;
use App\Tests\DatabaseDependantTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Refresh Stock Profile Command test
 * using Fake Yahoo Finance Api Client
 */
class RefreshStockProfileCommandTest extends DatabaseDependantTestCase
{
    /** @test */
    public function the_refresh_stock_profile_command_creates_new_record_correctly()
    {
        // SETUP
        $application = new Application(self::$kernel);

        // Command
        $command = $application->find('app:refresh-stock-profile');

        $commandTester = new CommandTester($command);

        // Set fake return content
        FakeYahooFinanceApiClient::$content = json_encode([
            'symbol'    => 'AMZN',
            'shortName'     => 'Amazon.com, Inc.',
            'region'        => 'US',
            'exchangeName'  => 'NasdaqGS',
            'currency'      => 'USD',
            'price'         => 100.50,
            'previousClose' => 110.20,
            'priceChange'   => -9.70,
        ]);

        // Do something
        $commandTester->execute([
            'symbol' => 'AMZN',
            'region' => 'US'
        ]);

        // Make assertions
        $repo = $this->entityManager->getRepository(Stock::class);

        /** @var Stock $stock */
        $stock = $repo->findOneBy(['symbol' => 'AMZN']);

        $this->assertEquals('Amazon.com, Inc.', $stock->getShortName());
        $this->assertEquals('USD', $stock->getCurrency());
        $this->assertEquals('NasdaqGS', $stock->getExchangeName());
        $this->assertEquals('US', $stock->getRegion());
        $this->assertIsFloat($stock->getPrice());
        $this->assertIsFloat($stock->getPreviousClose());
        $this->assertIsFloat($stock->getPriceChange());
        $this->assertStringContainsString('Amazon.com, Inc. has been saved / updated.', $commandTester->getDisplay());
    }

    /** @test */
    public function the_refresh_stodk_profile_command_creates_new_record_correctly()
    {
        // Setup
        // Set up
        $stock = new Stock();
        $stock->setSymbol('AMZN');
        $stock->setShortName('Amazon.com Inc.');
        $stock->setCurrency('USD');
        $stock->setExchangeName('NasdaqGS');
        $stock->setRegion('US');
        $stock->setPrice(3100);
        $stock->setPreviousClose(3000);
        $stock->setPriceChange(100);

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        $stockId = $stock->getId();

        $application = new Application(self::$kernel);

        $command = $application->find('app:refresh-stock-profile');

        $commandTester = new CommandTester($command);

        // Non 200 response
        FakeYahooFinanceApiClient::$statusCode = 200;
        FakeYahooFinanceApiClient::setContent([
            'previous_close' => 3100,
            'price' => 3200,
            'price_change' => 100
        ]);

        $commandStatus = $commandTester->execute([
            'symbol' => 'AMZN',
            'region' => 'US'
        ]);

        $repo = $this->entityManager->getRepository(Stock::class);

        $stockRecord = $repo->find($stockId);

        $stockRecordCount = $repo->createQueryBuilder('stock')
            ->select('count(stock.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(0, $commandStatus);
        $this->assertEquals(3100, $stockRecord->getPreviousClose());
        $this->assertEquals(3200, $stockRecord->getPrice());
        $this->assertEquals(100, $stockRecord->getPriceChange());
        $this->assertEquals(1, $stockRecordCount);
    }

    /** @test */
    public function non_200_status_code_response_are_handled_correctly()
    {
        // Setup
        $application = new Application(self::$kernel);

        $command = $application->find('app:refresh-stock-profile');

        $commandTester = new CommandTester($command);

        // Non 200 response
        FakeYahooFinanceApiClient::$statusCode = 500;
        FakeYahooFinanceApiClient::$content = 'Finance API Client Error.';

        $commandStatus = $commandTester->execute([
            'symbol' => 'AMZN',
            'region' => 'US'
        ]);

        // Do something
        $repo = $this->entityManager->getRepository(Stock::class);

        $stockRecordCount = $repo->createQueryBuilder('stock')
            ->select('count(stock.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Make assertion
        $this->assertEquals(1, $commandStatus);
        $this->assertEquals(0, $stockRecordCount);
        $this->assertStringContainsString('Finance API Client Error.', $commandTester->getDisplay());
    }
}