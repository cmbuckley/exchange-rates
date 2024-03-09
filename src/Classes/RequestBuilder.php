<?php

namespace AshAllenDesign\ExchangeRates\Classes;

use AshAllenDesign\ExchangeRates\Exceptions\ServiceException;
use GuzzleHttp\Client;

class RequestBuilder
{
    private const SERVICE_HOST = 'api.exchangerate.host';

    private Client $client;
    private array $options = [
        'tls'        => false, // not available on free plan
        'access_key' => '',
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set options for API access.
     *
     * @param string[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Make a HTTP request to the exchangerate.host API and return the
     * response body.
     *
     * @throws \AshAllenDesign\ExchangeRates\Exceptions\ServiceException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function makeRequest(string $path, array $queryParams = []): array
    {
        $queryParams['access_key'] = $this->options['access_key'];
        $scheme = $this->options['tls'] ? 'https' : 'http';
        $url = $scheme.'://'.static::SERVICE_HOST.'/'.$path.'?'.http_build_query($queryParams);

        $response = json_decode(
            $this->client->get($url)->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!$response['success']) {
            throw new ServiceException($response['error']['info'], $response['error']['code']);
        }

        return $response;
    }
}
