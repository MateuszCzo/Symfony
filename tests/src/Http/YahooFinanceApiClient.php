<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Yahoo Finance Api - make a connection to api and fetch data
 */
class YahooFinanceApiClient implements FinanceApiClientInterface
{
    private const URL = 'https://apidojo-yahoo-finance-v1.p.rapidapi.com/stock/v2/get-profile';
    private const X_RAPID_API_HOST = 'apidojo-yahoo-finance-v1.p.rapidapi.com';

    private $httpClient;
    private $rapidApiKey;

    public function __construct(HttpClientInterface $httpClient, string $rapidApiKey) {
        $this->httpClient = $httpClient;
        $this->rapidApiKey = $rapidApiKey;
    }

    public function fetchStockProfile(string $symbol, string $region): JsonResponse
    {
        /*
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
            return new JsonResponse('Finance API Client Error.', 400);
        }

        $stockProfile = json_decode($response->getContent());

        $stockProfileAsArray = [
            'symbol'        => $stockProfile->symbol,
            'shortName'     => $stockProfile->shortName,
            'region'        => $region,
            'exchangeName'  => $stockProfile->exchangeName,
            'currency'      => $stockProfile->currency,
            'price'         => $stockProfile->regularMarketPrice->raw,
            'previousClose' => $stockProfile->regularMarketPreviousClose->raw,
            'priceChange'   => $stockProfile->regularMarketPrice->raw - $stockProfile->regularMarketPreviousClose->raw,
        ];

        return new JsonResponse($stockProfileAsArray, 200);
        */

        $stockProfileAsArray = [
            'symbol'    => 'AMZN',
            'shortName'     => 'Amazon.com, Inc.',
            'region'        => 'US',
            'exchangeName'  => 'NasdaqGS',
            'currency'      => 'USD',
            'price'         => 3100.00,
            'previousClose' => 3000.00,
            'priceChange'   => 100.00,
        ];

        return new JsonResponse($stockProfileAsArray, 200);
    }
}