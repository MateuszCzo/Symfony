<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Fake Yahoo Finance Api - imitate a connection to the real api and returns data
 */
class FakeYahooFinanceApiClient implements FinanceApiClientInterface
{
    public static $statusCode = 200;
    public static $content = '';

    public function fetchStockProfile(string $symbol, string $region): JsonResponse
    {
        return new JsonResponse(self::$content, self::$statusCode, [], $json = true);
    }

    public static function setContent(array $overrides): void {
        self::$content = json_encode(array_merge([
            'symbol'    => 'AMZN',
            'short_name'     => 'Amazon.com, Inc.',
            'region'        => 'US',
            'exchange_name'  => 'NasdaqGS',
            'currency'      => 'USD',
            'price'         => 100.50,
            'previous_close' => 110.20,
            'price_change'   => -9.70,
        ], $overrides));
    }
}