<p align="center">
<img src="/docs/logo.png" alt="PHP Exchange Rates" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/cmbuckley/exchange-rates"><img src="https://img.shields.io/packagist/v/cmbuckley/exchange-rates.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://github.com/cmbuckley/exchange-rates"><img src="https://img.shields.io/github/actions/workflow/status/cmbuckley/exchange-rates/ci-tests.yml?style=flat-square" alt="Build Status"></a>
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
        - [Available Currencies](#available-currencies)
        - [Exchange Rate Between Two Currencies](#exchange-rate-between-two-currencies)
        - [Exchange Rate Between More Than Two Currencies](#exchange-rate-between-more-than-two-currencies)
        - [Exchange Rates Between Date Range](#exchange-rates-between-date-range)
        - [Convert Currencies](#convert-currencies)
        - [Convert Currencies Between Date Range](#convert-currencies-between-date-range)
- [Testing](#testing)
- [Contribution](#contribution)
- [Credits](#credits)
- [Changelog](#changelog)
- [License](#license)

## Overview

Exchange Rates is a simple PHP package used for interacting with the [exchangerate.host](https://exchangerate.host) API. You can use it to get the latest or historical exchange rates and convert monetary values between different currencies and cryptocurrencies.

## Installation

You can install the package via Composer:

```bash
composer require cmbuckley/exchange-rates
```

The package has been developed and tested to work with the following minimum requirements:

- PHP 8.2

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
To get the available currencies that are supported by the package, you can use the `currencies()` method like so:

```php
$exchangeRates->currencies();
```

#### Exchange Rate Between Two Currencies

To get the exchange for one currency to another, you can use the `exchangeRate()` method.

The example below shows how to get the exchange rate from GBP to EUR for today:

```php
$result = $exchangeRates->exchangeRate('GBP', 'EUR');

// $result: '1.10086'
```

If a valid date is passed as the third parameter, the exchange rate for that day will be returned. If no date is passed, today's exchange rate will be used.

#### Exchange Rate Between More Than Two Currencies

It is possible to get the exchange rates for multiple currencies in one call. This can be particularly useful if you are needing to get many exchange rates at once and do not want to make multiple API calls.

To do this, you can use `exchangeRate()` method and pass an array of currency code strings as the second parameter. This will return an array containing the exchange rates as strings:

```php
$result = $exchangeRates->exchangeRate('GBP', ['EUR', 'USD']);

// $result: [
//     'GBPEUR' => '1.10086',
//     'GBPUSD' => '1.25622'
// ];
```

As above, you can pass a date as the third parameter to get the exchange rates for that day.

#### Exchange Rates Between Date Range

To get the exchange rates between two currencies and a range of dates, you can use the `exchangeRateBetweenDateRange()` method.

The example below shows how to get the exchange rates from GBP to EUR for the past 3 days:

```php
$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    'EUR',
    (new DateTime)->sub(new DateInterval('P3D')),
    new DateTime
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

As before, you can pass an array of currency codes as the second parameter:
 
```php
$result = $exchangeRates->exchangeRateBetweenDateRange(
    'GBP',
    ['EUR', 'USD'],
    (new DateTime)->sub(new DateInterval('P3D')),
    new DateTime
);

// $result: [
//     '2020-07-07' => [
//         'GBPEUR' => '1.1092623405',
//         'GBPUSD' => '1.2523571825',
//      ],
//     '2020-07-08' => [
//         'GBPEUR' => '1.1120625424',
//         'GBPUSD' => '1.2550737853',
//      ],
//     '2020-07-09' => [
//         'GBPEUR' => '1.1153867604',
//         'GBPUSD' => '1.2650716636',
//      ],
// ];
```

#### Convert Currencies

Similar to how you can get the exchange rate from one currency to another, you can also convert a monetary value from one currency to another. To do this you can use the `convert()` method.

When passing in the monetary value (first parameter) that is to be converted, it's important that you pass it in the lowest denomination of that currency. For example, £1 GBP would be passed in as 100 (as £1 = 100 pence).

The example below shows how to convert £1 to EUR at today's exchange rate:

```php
$result = $exchangeRates->convert(100, 'GBP', 'EUR');

// $result: '110.15884906'
```

If a valid date is passed as the third parameter, the exchange rate for that day will be used. If no date is passed, today's exchange rate will be used.

You can also use the `convert()` method to convert a monetary value from one currency to multiple currencies. To do this, you can pass an array of currency codes strings as the third parameter:

```php
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

Similar to getting the exchange rates between a date range, you can also get convert monetary values from one currency to another using the exchange rates. To do this you can use the `convertBetweenDateRange()` method:

```php
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

You can also convert to multiple currencies:

```php
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
- Make all pull requests to the `main` branch.

## Credits

- [Chris Buckley](https://cmbuckley.co.uk)
- [Ash Allen](https://ashallendesign.co.uk)
- [Jess Pickup](https://jesspickup.co.uk) (Logo)
- [All Contributors](https://github.com/cmbuckley/exchange-rates/graphs/contributors)

## Changelog

Check the [CHANGELOG](CHANGELOG.md) to get more information about the latest changes.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
