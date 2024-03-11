<?php

namespace StarSquare\ExchangeRates\Tests\Unit;

use StarSquare\ExchangeRates\Classes\ExchangeRate;
use StarSquare\ExchangeRates\Classes\RequestBuilder;
use StarSquare\ExchangeRates\Exceptions\InvalidDateException;
use StarSquare\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ExchangeRateTest extends TestCase
{
    public function testExchangeRateForASingleCurrencyPairForTodayIsReturnedIfNoDateParameterIsPassed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['live', [
                'source'     => 'GBP',
                'currencies' => 'EUR',
            ]])
            ->andReturn($this->mockResponseForTodayForSingleCurrency());

        self::assertSame(
            '1.186864',
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', 'EUR'),
        );
    }

    public function testExchangeRateForMultipleCurrenciesForTodayIsReturnedIfNoDateParameterIsPassed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['live', [
                'source'     => 'GBP',
                'currencies' => 'EUR,USD',
            ]])
            ->andReturn($this->mockResponseForTodayForMultipleCurrencies());

        self::assertSame(
            [
                'GBPEUR' => '1.186864',
                'GBPUSD' => '1.376395',
            ],
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', ['EUR', 'USD']),
        );
    }

    public function testExchangeRateForASingleCurrencyPairInThePastIsReturnedIfADateParameterIsPassed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['historical', [
                'source'     => 'GBP',
                'currencies' => 'EUR',
                'date'       => '2021-10-25',
            ]])
            ->andReturn($this->mockResponseForYesterdayForSingleCurrency());

        self::assertSame(
            '1.186176',
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', 'EUR', Carbon::create(2021, 10, 25)),
        );
    }

    public function testExchangeRateForMultipleCurrenciesInThePastIsReturnedIfADateParameterIsPassed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['historical', [
                'source'     => 'GBP',
                'currencies' => 'EUR,USD',
                'date'       => '2021-10-25',
            ]])
            ->andReturn($this->mockResponseForYesterdayForMultipleCurrencies());

        self::assertSame(
            [
                'GBPEUR' => '1.186176',
                'GBPUSD' => '1.377304',
            ],
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', ['EUR', 'USD'], Carbon::create(2021, 10, 25)),
        );
    }

    public function testExceptionIsThrownIfTheDateIsInTheFuture(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', 'EUR', Carbon::now()->addDay());
    }

    private function mockResponseForTodayForSingleCurrency(): array
    {
        return [
            'success'   => true,
            'terms'     => 'https://exchangerate.host/terms',
            'privacy'   => 'https://exchangerate.host/privacy',
            'timestamp' => 1635246000,
            'source'    => 'GBP',
            'quotes'    => [
                'GBPEUR' => 1.186864,
            ],
        ];
    }

    private function mockResponseForYesterdayForSingleCurrency(): array
    {
        return [
            'success'    => true,
            'terms'      => 'https://exchangerate.host/terms',
            'privacy'    => 'https://exchangerate.host/privacy',
            'historical' => true,
            'date'       => '2021-10-25',
            'timestamp'  => 1635246000,
            'source'     => 'GBP',
            'quotes'     => [
                'GBPEUR' => 1.186176,
            ],
        ];
    }

    private function mockResponseForTodayForMultipleCurrencies(): array
    {
        return [
            'success'   => true,
            'terms'     => 'https://exchangerate.host/terms',
            'privacy'   => 'https://exchangerate.host/privacy',
            'timestamp' => 1635246000,
            'source'    => 'GBP',
            'quotes'    => [
                'GBPEUR' => 1.186864,
                'GBPUSD' => 1.376395,
            ],
        ];
    }

    private function mockResponseForYesterdayForMultipleCurrencies(): array
    {
        return [
            'success'    => true,
            'terms'      => 'https://exchangerate.host/terms',
            'privacy'    => 'https://exchangerate.host/privacy',
            'historical' => true,
            'date'       => '2021-10-25',
            'timestamp'  => 1635246000,
            'source'     => 'GBP',
            'quotes'     => [
                'GBPEUR' => 1.186176,
                'GBPUSD' => 1.377304,
            ],
        ];
    }
}
