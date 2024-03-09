<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ExchangeRateTest extends TestCase
{
    /** @test */
    public function exchange_rate_for_a_single_currency_pair_for_today_is_returned_if_no_date_parameter_is_passed(): void
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

    /** @test */
    public function exchange_rate_for_multiple_currencies_for_today_is_returned_if_no_date_parameter_is_passed(): void
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

    /** @test */
    public function exchange_rate_for_a_single_currency_pair_in_the_past_is_returned_if_a_date_parameter_is_passed(): void
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

    /** @test */
    public function exchange_rate_for_multiple_currencies_in_the_past_is_returned_if_a_date_parameter_is_passed(): void
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

    /** @test */
    public function exception_is_thrown_if_the_date_is_in_the_future(): void
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
