<?php

namespace CXml;

use CXml\Jms\CXmlWrappingNodeJmsEventSubscriber;
use CXml\Jms\JmsDateTimeHandler;
use CXml\Model\CXml;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

class Serializer
{
    private SerializerInterface $jmsSerializer;

    private function __construct(SerializerInterface $jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    public static function create(): self
    {
        $jmsSerializer = SerializerBuilder::create()
            ->configureListeners(static function (EventDispatcherInterface $dispatcher): void {
                $dispatcher->addSubscriber(new CXmlWrappingNodeJmsEventSubscriber());
            })
            ->configureHandlers(static function (HandlerRegistry $registry): void {
                $handler = new JmsDateTimeHandler();
                $callable = static function (XmlSerializationVisitor $visitor, \DateTimeInterface $date, array $type, Context $context) use ($handler): \DOMText {
                    return $handler->serialize($visitor, $date, $type, $context);
                };
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, \DateTimeInterface::class, 'xml', $callable);
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, \DateTime::class, 'xml', $callable);
                $callable = static function (XmlDeserializationVisitor $visitor, \SimpleXMLElement $dateAsString, array $type, Context $context) use ($handler): \DateTime|false {
                    return $handler->deserialize($visitor, $dateAsString, $type, $context);
                };
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, \DateTimeInterface::class, 'xml', $callable);
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, \DateTime::class, 'xml', $callable);
            })
            ->setPropertyNamingStrategy(
                new IdenticalPropertyNamingStrategy()
            )
            ->build()
        ;

        return new self($jmsSerializer);
    }

    public function deserialize(string $xml): CXml
    {
        // remove doctype (if exists), as it would throw a JMS\Serializer\Exception\InvalidArgumentException
        $xml = \preg_replace('/<!doctype[^>]+?>/i', '', $xml);

        if (empty($xml)) {
            throw new \RuntimeException('Cannot deserialize empty string');
        }

        /* @phpstan-ignore-next-line */
        return $this->jmsSerializer->deserialize($xml, CXml::class, 'xml');
    }

    public function serialize(CXml $cxml, string $docTypeVersion = '1.2.054'): string
    {
        $xml = $this->jmsSerializer->serialize($cxml, 'xml');

        $docType = '<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/'.$docTypeVersion.'/cXML.dtd">';
        $xmlPrefix = '<?xml version="1.0" encoding="UTF-8"?>';

        // add doctype, as it is mandatory in cXML
        return \str_replace($xmlPrefix, $xmlPrefix.$docType, $xml);
    }
}
