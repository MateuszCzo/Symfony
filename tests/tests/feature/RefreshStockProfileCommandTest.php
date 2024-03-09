<?php

namespace App\Tests\feature;

use App\Entity\Stock;
use App\Http\FakeYahooFinanceApiClient;
use App\Tests\DatabaseDependantTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RefreshStockProfileCommandTest extends DatabaseDependantTestCase
{
    /** @test */
    public function the_refresh_stock_profile_command_behaves_correctly_when_a_stock_record_does_not_exist()
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
    }

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
    }
}