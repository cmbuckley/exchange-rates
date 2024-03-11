<?php

namespace StarSquare\ExchangeRates\Classes;

use DateTimeInterface;
use GuzzleHttp\Client;

class ExchangeRate
{
    private RequestBuilder $requestBuilder;

    /**
     * @param RequestBuilder|null $requestBuilder
     */
    public function __construct(RequestBuilder $requestBuilder = null)
    {
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder(new Client());
    }

    /**
     * Set options on the request builder.
     *
     * @param string[] $options
     */
    public function setServiceOptions(array $options): void
    {
        $this->requestBuilder->setOptions($options);
    }

    /**
     * Get all the available supported currencies.
     *
     * @throws \StarSquare\ExchangeRates\Exceptions\ServiceException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     *
     * @return array
     */
    public function currencies(): array
    {
        return $this->requestBuilder->makeRequest('list')['currencies'];
    }

    /**
     * Find and return the exchange rate between currencies. If no date is
     * passed as the third parameter, today's exchange rate will be used.
     *
     * @param string                  $from
     * @param string|array            $to
     * @param \DateTimeInterface|null $date
     *
     * @throws \StarSquare\ExchangeRates\Exceptions\ServiceException
     * @throws \StarSquare\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     *
     * @return string|array
     */
    public function exchangeRate(string $from, string|array $to, DateTimeInterface $date = null): string|array
    {
        if ($date) {
            Validation::validateDate($date);
        }

        $symbols = is_string($to)
            ? $to
            : implode(',', $to);

        $queryParams = [
            'source'     => $from,
            'currencies' => $symbols,
        ];

        if ($date) {
            $queryParams['date'] = $date->format('Y-m-d');
        }

        $requestPath = $date ? 'historical' : 'live';
        $quotes = $this->requestBuilder->makeRequest($requestPath, $queryParams)['quotes'];

        if (is_string($to)) {
            return $quotes[$from.$to];
        }

        return array_map(static fn (string $item): string => $item, $quotes);
    }

    /**
     * Find and return the exchange rate between currencies between a given
     * date range.
     *
     * @param string             $from
     * @param string|array       $to
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     *
     * @throws \StarSquare\ExchangeRates\Exceptions\ServiceException
     * @throws \StarSquare\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     *
     * @return array
     */
    public function exchangeRateBetweenDateRange(string $from, string|array $to, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        Validation::validateStartAndEndDates($startDate, $endDate);

        $symbols = is_string($to) ? $to : implode(',', $to);

        $queryParams = [
            'source'     => $from,
            'currencies' => $symbols,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
        ];

        return $this->requestBuilder->makeRequest('timeframe', $queryParams)['quotes'];
    }

    /**
     * Convert a monetary value from one currency to another. If no date is
     * passed as the third parameter, today's exchange rate will be used.
     *
     * @param int                     $amount
     * @param string                  $from
     * @param string|array            $to
     * @param \DateTimeInterface|null $date
     *
     * @throws \StarSquare\ExchangeRates\Exceptions\ServiceException
     * @throws \StarSquare\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     *
     * @return string|array
     */
    public function convert(int $amount, string $from, string|array $to, DateTimeInterface $date = null): string|array
    {
        if ($date) {
            Validation::validateDate($date);
        }

        $exchangeRates = $this->exchangeRate($from, $to, $date);

        if (is_string($to)) {
            return $this->convertMoney($amount, $exchangeRates);
        }

        $converted = [];

        foreach ($exchangeRates as $currencyCode => $exchangeRate) {
            $converted[$currencyCode] = $this->convertMoney($amount, $exchangeRate);
        }

        return $converted;
    }

    /**
     * Convert monetary values from one currency to another using the exchange
     * rates between a given date range.
     *
     * @param int                $amount
     * @param string             $from
     * @param string|array       $to
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     *
     * @throws \StarSquare\ExchangeRates\Exceptions\ServiceException
     * @throws \StarSquare\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     *
     * @return array
     */
    public function convertBetweenDateRange(int $amount, string $from, string|array $to, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $to = is_array($to) ? $to : [$to];

        $exchangeRates = $this->exchangeRateBetweenDateRange($from, $to, $startDate, $endDate);

        return $this->convertCurrenciesOverDateRange($amount, $exchangeRates);
    }

    private function convertCurrenciesOverDateRange(int $amount, array $exchangeRates): array
    {
        $conversions = [];

        foreach ($exchangeRates as $date => $exchangeRate) {
            foreach ($exchangeRate as $currency => $rate) {
                $conversions[$date][$currency] = $this->convertMoney($amount, $rate);
            }
        }

        return $conversions;
    }

    private function convertMoney(string $amount, string $exchangeRate): string
    {
        return bcmul($amount, $exchangeRate, 8);
    }
}
