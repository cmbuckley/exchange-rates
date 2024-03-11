<?php

namespace StarSquare\ExchangeRates\Tests\Unit;

use StarSquare\ExchangeRates\Classes\ExchangeRate;
use StarSquare\ExchangeRates\Classes\RequestBuilder;
use StarSquare\ExchangeRates\Exceptions\InvalidDateException;
use StarSquare\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ExchangeRateBetweenDateRangeTest extends TestCase
{
    /** @test */
    public function exchange_rates_between_date_range_are_returned_for_a_single_currency(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['timeframe', [
                'source'     => 'GBP',
                'currencies' => 'EUR',
                'start_date' => '2021-10-19',
                'end_date'   => '2021-10-25',
            ]])
            ->andReturn($this->mockResponseForSingleCurrencyPair());

        self::assertEquals(
            $this->expectedForSingleCurrencyPair(),
            (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange(
                'GBP',
                'EUR',
                Carbon::create(2021, 10, 19),
                Carbon::create(2021, 10, 25)
            ),
        );
    }

    /** @test */
    public function exchange_rates_between_date_range_are_returned_for_multiple_currencies(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['timeframe', [
                'source'     => 'GBP',
                'currencies' => 'EUR,USD',
                'start_date' => '2021-10-19',
                'end_date'   => '2021-10-25',
            ]])
            ->andReturn($this->mockResponseForMultipleCurrencies());

        self::assertEquals(
            $this->expectedForMultipleCurrencies(),
            (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange(
                'GBP',
                ['EUR', 'USD'],
                Carbon::create(2021, 10, 19),
                Carbon::create(2021, 10, 25)
            ),
        );
    }

    /** @test */
    public function exception_is_thrown_if_the_start_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->addDay(), Carbon::now()->addDays(2));
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subDay(), Carbon::now()->addDays(2));
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_is_before_the_start_date(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subDay(), Carbon::now()->subDays(2));
    }

    private function mockResponseForSingleCurrencyPair(): array
    {
        return [
            'success'    => true,
            'terms'      => 'https://exchangerate.host/terms',
            'privacy'    => 'https://exchangerate.host/privacy',
            'timeframe'  => true,
            'start_date' => '2021-10-19',
            'end_date'   => '2021-10-25',
            'source'     => 'GBP',
            'quotes'     => [
                '2021-10-19' => [
                    'GBPEUR' => 1.186206,
                ],
                '2021-10-20' => [
                    'GBPEUR' => 1.18663,
                ],
                '2021-10-21' => [
                    'GBPEUR' => 1.18649,
                ],
                '2021-10-22' => [
                    'GBPEUR' => 1.181421,
                ],
                '2021-10-23' => [
                    'GBPEUR' => 1.181848,
                ],
                '2021-10-24' => [
                    'GBPEUR' => 1.1813,
                ],
                '2021-10-25' => [
                    'GBPEUR' => 1.186176,
                ],
            ],
        ];
    }

    private function mockResponseForMultipleCurrencies(): array
    {
        return [
            'success'    => true,
            'terms'      => 'https://exchangerate.host/terms',
            'privacy'    => 'https://exchangerate.host/privacy',
            'timeframe'  => true,
            'start_date' => '2021-10-19',
            'end_date'   => '2021-10-25',
            'source'     => 'GBP',
            'quotes'     => [
                '2021-10-19' => [
                    'GBPEUR' => 1.186206,
                    'GBPUSD' => 1.381227,
                ],
                '2021-10-20' => [
                    'GBPEUR' => 1.18663,
                    'GBPUSD' => 1.382278,
                ],
                '2021-10-21' => [
                    'GBPEUR' => 1.18649,
                    'GBPUSD' => 1.378447,
                ],
                '2021-10-22' => [
                    'GBPEUR' => 1.181421,
                    'GBPUSD' => 1.375148,
                ],
                '2021-10-23' => [
                    'GBPEUR' => 1.181848,
                    'GBPUSD' => 1.375865,
                ],
                '2021-10-24' => [
                    'GBPEUR' => 1.1813,
                    'GBPUSD' => 1.375172,
                ],
                '2021-10-25' => [
                    'GBPEUR' => 1.186176,
                    'GBPUSD' => 1.377304,
                ],
            ],
        ];
    }

    private function expectedForSingleCurrencyPair(): array
    {
        return [
            '2021-10-19' => [
                'GBPEUR' => '1.186206',
            ],
            '2021-10-20' => [
                'GBPEUR' => '1.18663',
            ],
            '2021-10-21' => [
                'GBPEUR' => '1.18649',
            ],
            '2021-10-22' => [
                'GBPEUR' => '1.181421',
            ],
            '2021-10-23' => [
                'GBPEUR' => '1.181848',
            ],
            '2021-10-24' => [
                'GBPEUR' => '1.1813',
            ],
            '2021-10-25' => [
                'GBPEUR' => '1.186176',
            ],
        ];
    }

    private function expectedForMultipleCurrencies(): array
    {
        return [
            '2021-10-19' => [
                'GBPEUR' => '1.186206',
                'GBPUSD' => '1.381227',
            ],
            '2021-10-20' => [
                'GBPEUR' => '1.18663',
                'GBPUSD' => '1.382278',
            ],
            '2021-10-21' => [
                'GBPEUR' => '1.18649',
                'GBPUSD' => '1.378447',
            ],
            '2021-10-22' => [
                'GBPEUR' => '1.181421',
                'GBPUSD' => '1.375148',
            ],
            '2021-10-23' => [
                'GBPEUR' => '1.181848',
                'GBPUSD' => '1.375865',
            ],
            '2021-10-24' => [
                'GBPEUR' => '1.1813',
                'GBPUSD' => '1.375172',
            ],
            '2021-10-25' => [
                'GBPEUR' => '1.186176',
                'GBPUSD' => '1.377304',
            ],
        ];
    }
}
