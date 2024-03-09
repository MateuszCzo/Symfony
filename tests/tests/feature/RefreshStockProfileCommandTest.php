<?php

namespace App\Tests\feature;

use App\Entity\Stock;
use App\Tests\DatabaseDependantTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RefreshStockProfileCommandTest extends DatabaseDependantTestCase
{
    /** @test */
    public function the_refresh_stock_profile_command_behaves_correctly_when_a_stock_record_does_not_exist()
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:refresh-stock-profile');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'symbol' => 'AMZN',
            'region' => 'US'
        ]);

        $repo = $this->entityManager->getRepository(Stock::class);

        /** @var Stock $stock */
        $stock = $repo->findOneBy(['symbol' => 'AMZN']);

        $this->assertEquals('Amazon Inc.', $stock->getShortName());
        $this->assertEquals('USD', $stock->getCurrency());
        $this->assertEquals('NasdaqGS', $stock->getExchangeName());
        $this->assertEquals('US', $stock->getRegion());
        $this->assertEquals(1000, $stock->getPrice());
        $this->assertEquals(1100, $stock->getPreviousClose());
        $this->assertEquals(-100, $stock->getPriceChange());
    }
}