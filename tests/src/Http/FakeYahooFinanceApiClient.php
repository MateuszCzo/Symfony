<?php

namespace App\Http;

class FakeYahooFinanceApiClient implements FinanceApiClientInterface
{
    public static $statusCode = 200;
    public static $content = '';

    public function fetchStockProfile(string $symbol, string $region): array
    {
        return [
            'statusCode'    => self::$statusCode,
            'content'       => self::$content,
        ];
    }
}