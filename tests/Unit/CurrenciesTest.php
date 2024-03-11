<?php

namespace StarSquare\ExchangeRates\Tests\Unit;

use StarSquare\ExchangeRates\Classes\ExchangeRate;
use StarSquare\ExchangeRates\Classes\RequestBuilder;
use StarSquare\ExchangeRates\Tests\TestCase;

class CurrenciesTest extends TestCase
{
    public function testCurrenciesAreReturned(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->withArgs(['list'])
            ->once()
            ->andReturn($this->mockResponse());

        self::assertSame(
            $this->expectedResponse(),
            (new ExchangeRate($requestBuilderMock))->currencies(),
        );
    }

    private function mockResponse(): array
    {
        return [
            'success'    => true,
            'terms'      => 'https://exchangerate.host/terms',
            'privacy'    => 'https://exchangerate.host/privacy',
            'currencies' => [
                'AED' => 'United Arab Emirates Dirham',
                'AFN' => 'Afghan Afghani',
                'ALL' => 'Albanian Lek',
            ],
        ];
    }

    private function expectedResponse(): array
    {
        return [
            'AED' => 'United Arab Emirates Dirham',
            'AFN' => 'Afghan Afghani',
            'ALL' => 'Albanian Lek',
        ];
    }
}
