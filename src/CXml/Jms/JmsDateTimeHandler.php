<?php

namespace CXml\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * We need a custom DateTime Handler to allow also milliseconds as well as ISO-8601.
 *
 * Although the cXML documentation defines ISO-8601 as the primary date format, there are cXML implementations
 * which uses milliseconds (i.e. https://www.jaggaer.com/)
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
		$dateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s.vP', $dateAsString);

		if ($dateTime === false) {
			$dateTime = \DateTime::createFromFormat(\DateTime::ATOM, $dateAsString);
		}

		return $dateTime;
	}

}