<?php

namespace App\Tests\integration;

use App\Tests\DatabaseDependantTestCase;

class YahooFinanceApiClientTest extends DatabaseDependantTestCase
{
    /**
     * @test
     * @group integration
     */
    public function the_yahoo_finanfe_api_clinet_returns_the_correct_data()
    {
        /** @var FinanceApiClientInterface $financeApiClient */
        $financeApiClient = self::$kernel->getContainer()->get('yahoo-finance-api-client');

        $response = $financeApiClient->fetchStockProfile('AMZN', 'US');

        $stockProfile = json_decode($response['content']);

        $this->assertSame('AMZN', $stockProfile->symbol);
        $this->assertSame('Amazon.com, Inc.', $stockProfile->shortName);
        $this->assertSame('US', $stockProfile->region);
        $this->assertSame('NasdaqGS', $stockProfile->exchangeName);
        $this->assertSame('USD', $stockProfile->currency);
        $this->assertIsFloat($stockProfile->price);
        $this->assertIsFloat($stockProfile->previousClose);
        $this->assertIsFloat($stockProfile->priceChange);
    }
}