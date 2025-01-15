<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTime;
use DateTimeZone;

/**
 * Represents a date *without* time. This is a separate class to allow for different serialization formats.
 */
class Date extends DateTime
{
    public static function createFromFormat($format, $datetime, ?DateTimeZone $timezone = null): DateTime|false|Date
    {
        $dateTime = parent::createFromFormat($format, $datetime, $timezone);
        if (false === $dateTime) {
            return false;
        }

        return new self($dateTime->format('Y-m-d'), $timezone);
    }
}
