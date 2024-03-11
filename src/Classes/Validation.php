<?php

namespace StarSquare\ExchangeRates\Classes;

use DateTime;
use DateTimeInterface;
use StarSquare\ExchangeRates\Exceptions\InvalidDateException;

class Validation
{
    /**
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @throws InvalidDateException
     */
    public static function validateStartAndEndDates(DateTimeInterface $from, DateTimeInterface $to): void
    {
        self::validateDate($from);
        self::validateDate($to);

        if ($from > $to) {
            throw new InvalidDateException('The \'from\' date must be before the \'to\' date.');
        }
    }

    /**
     * @param \DateTimeInterface $date
     *
     * @throws InvalidDateException
     */
    public static function validateDate(DateTimeInterface $date): void
    {
        $today = new DateTime();
        $today->setTime(0, 0);

        if ($date > $today) {
            throw new InvalidDateException('The date must be in the past.');
        }

        $earliestPossibleDate = new DateTime('1999-01-04');

        if ($date < $earliestPossibleDate) {
            throw new InvalidDateException('The date cannot be before 4th January 1999.');
        }
    }
}
