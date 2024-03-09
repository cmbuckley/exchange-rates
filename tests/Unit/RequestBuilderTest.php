<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Exceptions\ServiceException;
use AshAllenDesign\ExchangeRates\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class RequestBuilderTest extends TestCase
{
    /** @test */
    public function service_url_should_respect_options(): void
    {
        $handler = new MockHandler([
            new Response(200, [], $this->mockSuccessResponse()),
            new Response(200, [], $this->mockSuccessResponse()),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($handler),
        ]);

        $requestBuilder = new RequestBuilder($client);

        $this->assertSame(
            $this->expectedSuccessResponse(),
            $requestBuilder->makeRequest('timeframe', [
                'source'     => 'GBP',
                'currencies' => 'EUR',
                'start_date' => '2021-10-19',
                'end_date'   => '2021-10-20',
            ])
        );

        $this->assertSame(
            'http://api.exchangerate.host/timeframe?source=GBP&currencies=EUR&start_date=2021-10-19&end_date=2021-10-20&access_key=',
            strval($handler->getLastRequest()->getUri())
        );

        $requestBuilder->setOptions([
            'tls'        => true,
            'access_key' => '123',
        ]);

        $requestBuilder->makeRequest('timeframe', [
            'foo' => 'bar',
        ]);

        $this->assertSame(
            'https://api.exchangerate.host/timeframe?foo=bar&access_key=123',
            strval($handler->getLastRequest()->getUri())
        );
    }

    /** @test */
    public function should_throw_exception_on_bad_response(): void
    {
        $handler = new MockHandler([
            new Response(200, [], $this->mockErrorResponse()),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($handler),
        ]);

        $requestBuilder = new RequestBuilder($client);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage('You have not supplied an API Access Key');
        $this->expectExceptionCode(101);

        $requestBuilder->makeRequest('timeframe');
    }

    private function mockSuccessResponse(): string
    {
        return json_encode($this->expectedSuccessResponse());
    }

    private function mockErrorResponse(): string
    {
        return json_encode([
            'success' => false,
            'error'   => [
                'code' => 101,
                'type' => 'missing_access_key',
                'info' => 'You have not supplied an API Access Key',
            ],
        ]);
    }

    private function expectedSuccessResponse(): array
    {
        return [
            'success'    => true,
            'terms'      => 'https://exchangerate.host/terms',
            'privacy'    => 'https://exchangerate.host/privacy',
            'timeframe'  => true,
            'start_date' => '2021-10-19',
            'end_date'   => '2021-10-20',
            'source'     => 'GBP',
            'quotes'     => [
                '2021-10-19' => [
                    'GBPEUR' => 1.186206,
                ],
                '2021-10-20' => [
                    'GBPEUR' => 1.18663,
                ],
            ],
        ];
    }
}
