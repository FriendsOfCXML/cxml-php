<?php

namespace CXml\Model;

if (\PHP_VERSION_ID < 80000) {
	/**
	 * Represents a date *without* time. This is a separate class to allow for different serialization formats.
	 */
	class Date extends \DateTime
	{
		#[\ReturnTypeWillChange]
		public static function createFromFormat($format, $datetime, \DateTimeZone $timezone = null)
		{
			$dateTime = parent::createFromFormat($format, $datetime, $timezone);
			if (!$dateTime) {
				return false;
			}

			return new self($dateTime->format('Y-m-d'), $timezone);
		}

		public static function createFromInterface(\DateTimeInterface $object): \DateTime
		{
			return new self($object->format('Y-m-d'), $object->getTimezone());
		}
	}
} else {
	/**
	 * Represents a date *without* time. This is a separate class to allow for different serialization formats.
	 */
	class Date extends \DateTime
	{
		#[\ReturnTypeWillChange]
		public static function createFromFormat(string $format, string $datetime, \DateTimeZone $timezone = null)
		{
			$dateTime = parent::createFromFormat($format, $datetime, $timezone);
			if (!$dateTime) {
				return false;
			}

			return new self($dateTime->format('Y-m-d'), $timezone);
		}
	}
}
