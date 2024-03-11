<p align="center">
<img src="/docs/logo.png" alt="PHP Exchange Rates" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/cmbuckley/exchange-rates"><img src="https://img.shields.io/packagist/v/cmbuckley/exchange-rates.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://github.com/cmbuckley/exchange-rates"><img src="https://img.shields.io/github/actions/workflow/status/cmbuckley/exchange-rates/ci-tests?style=flat-square" alt="Build Status"></a>
<a href="https://packagist.org/packages/cmbuckley/exchange-rates"><img src="https://img.shields.io/packagist/dt/cmbuckley/exchange-rates.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/cmbuckley/exchange-rates"><img src="https://img.shields.io/packagist/php-v/cmbuckley/exchange-rates?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://github.com/cmbuckley/exchange-rates/blob/main/LICENSE.md"><img src="https://img.shields.io/github/license/cmbuckley/exchange-rates?style=flat-square" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Usage](#usage)
    - [Setup](#setup)
    - [Methods](#methods)
        - [Exchange Rate](#exchange-rate)
            - [Getting the Rate Between Two Currencies](#getting-the-rate-between-two-currencies)
            - [Getting the Rate Between More Than Two Currencies](#getting-the-rate-between-more-than-two-currencies)
        - [Exchange Rates Between Date Range](#exchange-rates-between-date-range)
            - [Getting the Rates Between Two Currencies](#getting-the-rates-between-two-currencies)
            - [Getting the Rates Between More Than Two Currencies](#getting-the-rates-between-more-than-two-currencies)
        - [Convert Currencies](#convert-currencies)
            - [Converting Between Two Currencies](#converting-between-two-currencies)
            - [Converting Between More Than Two Currencies](#converting-between-more-than-two-currencies)
        - [Convert Currencies Between Date Range](#convert-currencies-between-date-range)
            - [Converting Between Two Currencies in a Date Range](#converting-between-two-currencies-in-a-date-range)
            - [Converting Between More Than Two Currencies in a Date Range](#converting-between-more-than-two-currencies-in-a-date-range)
- [Testing](#testing)
- [Security](#security)
- [Contribution](#contribution)
- [Credits](#credits)
- [Changelog](#changelog)
- [Upgrading](#upgrading)
- [License](#license)

## Overview

Exchange Rates is a simple PHP package used for interacting with the [exchangerate.host](https://exchangerate.host) API. You can use it to get the latest or historical exchange rates and convert monetary values between different currencies and cryptocurrencies.

## Installation

You can install the package via Composer:

```bash
composer require cmbuckley/exchange-rates
```

The package has been developed and tested to work with the following minimum requirements:

- PHP 8.0

## Usage

### Setup

The [exchangerate.host](https://exchangerate.host) service requires an access key, which you can sign up and request
for free. You need to add a payment card to get the key, but you won't be charged if you're on the free plan. You are
resticted to 100 requests per month on the free plan.

Set the API key as follows:

```php
use StarSquare\ExchangeRates\Classes\ExchangeRate;

$exchangeRates = new ExchangeRate();

$exchangeRates->setServiceOptions([
    'access_key' => '123abc',
]);
```

The API also does not support HTTPS on the free plan. If you are on a paid plan and want to use HTTPS, pass the `tls`
service option:

```php
$exchangeRates->setServiceOptions([
    'access_key' => '123abc',
    'tls'        => true,
]);
```

### Methods

#### Available Currencies
To get the available currencies that are supported by the package, you can use the `->currencies()` method like so:

```php
$exchangeRates = new ExchangeRate();

$exchangeRates->currencies();
```

#### Exchange Rate

##### Getting the Rate Between Two Currencies

To get the exchange for one currency to another, you can use the `->exchangeRate()` method.

The example below shows how to get the exchange rate from 'GBP' to 'EUR' for today:

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->exchangeRate('GBP', 'EUR');

// $result: '1.10086'
```

Note: If a Carbon date is passed as the third parameter, the exchange rate for that day will be returned (if valid). If no date is passed, today's exchange rate will be used.

##### Getting the Rate Between More Than Two Currencies

It is possible to get the exchange rates for multiple currencies in one call. This can be particularly useful if you are needing to get many exchange rates at once and do not want to make multiple API calls.

To do this, you can use `->exchangeRate()` method and pass an array of currency code strings as the second parameter. This will return an array containing the exchange rates as strings.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' and 'USD' for today.

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->exchangeRate('GBP', ['EUR', 'USD']);

// $result: [
//     'GBPEUR' => '1.10086',
//     'GBPUSD' => '1.25622'
// ];
```

#### Exchange Rates Between Date Range

##### Getting the Rates Between Two Currencies

To get the exchange rates between two currencies between a given date range, you can use the `->exchangeRateBetweenDateRange()` method.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' for the past 3 days:

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    'EUR',
    Carbon::now()->subWeek(),
    Carbon::now()
);

// $result: [
//     '2020-07-07' => [
//         'GBPEUR' => '1.1092623405',
//     ],
//     '2020-07-08' => [
//         'GBPEUR' => '1.1120625424',
//     ],
//     '2020-07-09' => [
//         'GBPEUR' => '1.1153867604',
//     ],
// ];
```

##### Getting the Rates Between More Than Two Currencies

To get the exchange rates for multiple currencies in one call, you can pass an array of currency codes strings as the second parameter to the `->exchangeRateBetweenDateRange()` method.

The example below shows how to get the exchange rates from 'GBP' to 'EUR' and 'USD' for the past 3 days:

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    ['EUR', 'USD'],
    Carbon::now()->subDays(3),
    Carbon::now(),
);

// $result: [
//     '2020-07-07' => [
//         'GBPEUR' => '1.1092623405',
//         'GBPUSD' => '1.2523571825',
//      ],
//     '2020-07-08' => [
//         'EUR' => '1.1120625424',
//         'GBPUSD' => '1.2550737853',
//      ],
//     '2020-07-09' => [
//         'EUR' => '1.1153867604',
//         'GBPUSD' => '1.2650716636',
//      ],
// ];
```

#### Convert Currencies

When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

##### Converting Between Two Currencies

Similar to how you can get the exchange rate from one currency to another, you can also convert a monetary value from one currency to another. To do this you can use the `->convert()` method.

The example below shows how to convert £1 'GBP' to 'EUR' at today's exchange rate:

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->convert(100, 'GBP', 'EUR');

// $result: '110.15884906'
```

Note: If a Carbon date object is passed as the third parameter, the exchange rate for that day will be used (if valid). If no date is passed, today's exchange rate will be used.

##### Converting Between More Than Two Currencies

You can also use the `->convert()` method to convert a monetary value from one currency to multiple currencies. To do this, you can pass an array of currency codes strings as the third parameter.

The example below show how to convert £1 'GBP' to 'EUR' and 'USD' at today's exchange rate:

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->convert(
    100,
    'GBP',
    ['EUR', 'USD']
);

// $result: [
//     'GBPEUR' => '110.15884906',
//     'GBPUSD' => '125.30569081'
// ];
```

#### Convert Currencies Between Date Range

When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

##### Converting Between Two Currencies in a Date Range

Similar to getting the exchange rates between a date range, you can also get convert monetary values from one currency to another using the exchange rates. To do this you can use the ``` ->convertBetweenDateRange() ``` method.

The example below shows how to convert £1 'GBP' to 'EUR' using the exchange rates for the past 3 days:

```php
$exchangeRates = new ExchangeRate();

$result = $exchangeRates->convertBetweenDateRange(
    100,
    'GBP',
    'EUR',
    (new DateTime)->sub(new DateInterval('P3D')),
    new DateTime
);

// $result: [
//     '2020-07-07' => [
//         'GBPEUR' => '110.92623405',
//      ],
//     '2020-07-08' => [
//         'GBPEUR' => '111.20625424',
//      ],
//     '2020-07-09' => [
//         'GBPEUR' => '111.53867604',
//      ],
// ];
```

##### Converting Between More Than Two Currencies in a Date Range

You can also use the `->convertBetweenDateRange()` method to convert a monetary value from one currency to multiple currencies using the exchange rates between a date range. To do this, you can pass an array of currency codes strings as the third parameter.

The example below show how to convert £1 'GBP' to 'EUR' and 'USD' at the past three days' exchange rates:

```php
$exchangeRates = new ExchangeRate();
$result = $exchangeRates->convertBetweenDateRange(
    100,
    'GBP',
    ['EUR', 'USD'],
    (new DateTime)->sub(new DateInterval('P3D')),
    new DateTime
);

// $result: [
//     '2020-07-07' => [
//         'GBPEUR' => '110.92623405',
//         'GBPUSD' => '125.23571825',
//      ],
//     '2020-07-08' => [
//         'GBPEUR' => '111.20625424',
//         'GBPUSD' => '125.50737853',
//      ],
//     '2020-07-09' => [
//         'GBPEUR' => '111.53867604',
//         'GBPUSD' => '126.50716636',
//      ],
// ];
```

## Testing

To run the tests for the package, you can use the following command:

```bash
composer test
```

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

To contribute to this library, please use the following guidelines before submitting your pull request:

- Write tests for any new functions that are added. If you are updating existing code, make sure that the existing tests
  pass and write more if needed.
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
- Make all pull requests to the ``` master ``` branch.

## Credits

- [Chris Buckley](https://cmbuckley.co.uk)
- [Ash Allen](https://ashallendesign.co.uk)
- [Jess Pickup](https://jesspickup.co.uk) (Logo)
- [All Contributors](https://github.com/cmbuckley/exchange-rates/graphs/contributors)

## Changelog

Check the [CHANGELOG](CHANGELOG.md) to get more information about the latest changes.

## Upgrading

Check the [UPGRADE](UPGRADE.md) guide to get more information on how to update this library to newer versions.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
