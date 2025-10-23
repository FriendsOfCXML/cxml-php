<?php

declare(strict_types=1);

namespace CXml\Jms;

use CXml\Model\Date;
use DateTime;
use DateTimeInterface;
use DOMText;
use JMS\Serializer\Context;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use RuntimeException;
use SimpleXMLElement;

/**
 * We need a custom DateTime Handler to allow multiple different DateTime versions.
 *
 * Although the cXML documentation defines ISO-8601 as the primary date format, there are cXML implementations
 * that use milliseconds or a simple date-only format. (i.e., https://www.jaggaer.com/)
 */
class JmsDateTimeHandler
{
    public function serialize(XmlSerializationVisitor $visitor, DateTimeInterface $date, array $type, Context $context): DOMText
    {
        $format = $date instanceof Date ? 'Y-m-d' : $this->getFormat($type);

        /* @phpstan-ignore-next-line */
        return $visitor->visitSimpleString($date->format($format), $type);
    }

    private function getFormat(array $type): string
    {
        if (isset($type['params']) && is_array($type['params']) && [] !== $type['params']) {
            $format = $type['params'][0] ?? null;

            if (is_string($format) && '' !== $format) {
                return $format;
            }
        }

        // if no format was defined, use ISO-8601 as default
        return DateTimeInterface::ATOM;
    }

    public function deserialize(XmlDeserializationVisitor $visitor, SimpleXMLElement $dateAsString, array $type, Context $context): DateTime|false
    {
        // explicit date-format was defined in property annotation
        if (isset($type['params']) && is_array($type['params']) && [] !== $type['params'] && (isset($type['params'][0]) && is_string($type['params'][0]) && '' !== $type['params'][0])) {
            return DateTime::createFromFormat($type['params'][0], $dateAsString->__toString());
        }

        // else try ISO-8601
        $dateTime = DateTime::createFromFormat(DateTimeInterface::ATOM, $dateAsString->__toString());
        if ($dateTime instanceof DateTimeInterface) {
            return $dateTime;
        }

        // else try milliseconds-format with Timezone
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s.vP', $dateAsString->__toString());
        if ($dateTime instanceof DateTimeInterface) {
            return $dateTime;
        }

        // else try milliseconds-format without Timezone
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s.v', $dateAsString->__toString());
        if ($dateTime instanceof DateTimeInterface) {
            return $dateTime;
        }

        // else try simple datetime-format
        $dateTime = Date::createFromFormat('Y-m-d H:i:s', $dateAsString->__toString());
        if ($dateTime instanceof DateTimeInterface) {
            return $dateTime;
        }

        // else try simple date-format
        $dateTime = Date::createFromFormat('Y-m-d', $dateAsString->__toString());
        if ($dateTime instanceof DateTimeInterface) {
            return $dateTime;
        }

        // last resort: throw exception
        throw new RuntimeException('Could not parse date: ' . $dateAsString->__toString());
    }
}
