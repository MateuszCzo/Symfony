<?php

namespace App\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class YahooFinanceApiClient implements FinanceApiClientInterface
{
    private const URL = 'https://apidojo-yahoo-finance-v1.p.rapidapi.com/stock/v2/get-profile';
    private const X_RAPID_API_HOST = 'apidojo-yahoo-finance-v1.p.rapidapi.com';

    private $httpClient;
    private $rapidApiKey;

    public function __construct(HttpClientInterface $httpClient, $rapidApiKey) {
        $this->httpClient = $httpClient;
        $this->rapidApiKey = $rapidApiKey;
    }

    public function fetchStockProfile(string $symbol, string $region): array
    {
        $response = $this->httpClient->request('GET', self::URL . $symbol, [
            'query' => [
                'symbol' => $symbol,
                'region' => $region,
            ],
            'headers' => [
                'x-rapidapi-host' => self::X_RAPID_API_HOST,
                'x-rapidapi-key' => $this->rapidApiKey,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {

        }

        $stockProfile = json_decode($response->getContent());

        return [
            'statusCode'    => 200,
            'content'       => json_encode([
                'symbol'        => $stockProfile->symbol,
                'shortName'     => $stockProfile->shortName,
                'region'        => $region,
                'exchangeName'  => $stockProfile->exchangeName,
                'currency'      => $stockProfile->currency,
                'price'         => $stockProfile->regularMarketPrice->raw,
                'previousClose' => $stockProfile->regularMarketPreviousClose->raw,
                'priceChange'   => $stockProfile->regularMarketPrice->raw - $stockProfile->regularMarketPreviousClose->raw,
            ]),
        ];
    }
}