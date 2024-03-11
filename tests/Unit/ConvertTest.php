<?php

namespace StarSquare\ExchangeRates\Tests\Unit;

use StarSquare\ExchangeRates\Classes\ExchangeRate;
use StarSquare\ExchangeRates\Classes\RequestBuilder;
use StarSquare\ExchangeRates\Exceptions\InvalidDateException;
use StarSquare\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ConvertTest extends TestCase
{
    public function testConvertedValueForTodayIsReturnedIfNoDateParameterIsPassed(): void
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
            '118.68640000',
            (new ExchangeRate($requestBuilderMock))->convert(100, 'GBP', 'EUR'),
        );
    }

    public function testConvertedValueInThePastIsReturnedIfTheDateParameterIsPassed(): void
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
                'GBPEUR' => '118.68640000',
                'GBPUSD' => '137.63950000',
            ],
            (new ExchangeRate($requestBuilderMock))->convert(100, 'GBP', ['EUR', 'USD']),
        );
    }

    public function testConvertedValuesAreReturnedForTodayWithMultipleCurrencies(): void
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
            '118.61760000',
            (new ExchangeRate($requestBuilderMock))->convert(100, 'GBP', 'EUR', Carbon::create(2021, 10, 25)),
        );
    }

    public function testConvertedValuesAreReturnedForYesterdayWithMultipleCurrencies(): void
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
                'GBPEUR' => '118.61760000',
                'GBPUSD' => '137.73040000',
            ],
            (new ExchangeRate($requestBuilderMock))->convert(100, 'GBP', ['EUR', 'USD'], Carbon::create(2021, 10, 25)),
        );
    }

    public function testExceptionIsThrownIfTheDateParameterPassedIsInTheFuture(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->convert(100, 'GBP', 'EUR', Carbon::now()->addDay());
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
