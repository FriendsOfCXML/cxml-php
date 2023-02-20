<?php

namespace CXml\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * We need a custom DateTime Handler to allow multiple different DateTime versions.
 *
 * Although the cXML documentation defines ISO-8601 as the primary date format, there are cXML implementations
 * which uses milliseconds or a simple date-only format. (i.e. https://www.jaggaer.com/)
 */
class JmsDateTimeHandler
{
	public function serialize(XmlSerializationVisitor $visitor, \DateTime $date, array $type, Context $context)
	{
		return $visitor->visitSimpleString($date->format($this->getFormat($type)), $type);
	}

	private function getFormat(array $type): string
	{
		return $type['params'][0] ?? \DateTime::ATOM;
	}

	public function deserialize(XmlDeserializationVisitor $visitor, $dateAsString, array $type, Context $context)
	{
		// explicit date-format was defined in property annotation
		if (isset($type['params'][0])) {
			return \DateTime::createFromFormat($type['params'][0], $dateAsString);
		}

		// else try ISO-8601
		$dateTime = \DateTime::createFromFormat(\DateTime::ATOM, $dateAsString);
		if ($dateTime) {
			return $dateTime;
		}

		// else try milliseconds-format
		$dateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s.vP', $dateAsString);
		if ($dateTime) {
			return $dateTime;
		}

		// else try simple date-format
		$dateTime = \DateTime::createFromFormat('Y-m-d', $dateAsString);
		if ($dateTime) {
			$dateTime->setTime(0, 0, 0);

			return $dateTime;
		}

		return null;
	}
}
