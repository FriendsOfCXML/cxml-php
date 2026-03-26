<?php

declare(strict_types=1);

namespace CXml;

use CXml\Jms\CXmlExclusionStrategy;
use CXml\Jms\CXmlWrappingNodeJmsEventSubscriber;
use CXml\Jms\JmsDateTimeHandler;
use CXml\Model\CXml;
use CXml\Model\Index;
use DateTime;
use DateTimeInterface;
use DOMText;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use RuntimeException;
use SimpleXMLElement;

use function preg_replace;
use function str_replace;
use function trim;

readonly class Serializer
{
    private function __construct(
        private SerializerInterface $jmsSerializer,
    ) {
    }

    public static function create(): self
    {
        $jmsSerializer = SerializerBuilder::create()
            ->configureListeners(static function (EventDispatcherInterface $dispatcher): void {
                $dispatcher->addSubscriber(new CXmlWrappingNodeJmsEventSubscriber());
            })
            ->configureHandlers(static function (HandlerRegistry $registry): void {
                $handler = new JmsDateTimeHandler();
                $callable = static fn (XmlSerializationVisitor $visitor, DateTimeInterface $date, array $type, Context $context): DOMText => $handler->serialize($visitor, $date, $type, $context);
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, DateTimeInterface::class, 'xml', $callable);
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_SERIALIZATION, DateTime::class, 'xml', $callable);

                $callable = static fn (XmlDeserializationVisitor $visitor, SimpleXMLElement $dateAsString, array $type, Context $context): DateTime|false => $handler->deserialize($visitor, $dateAsString, $type, $context);
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, DateTimeInterface::class, 'xml', $callable);
                $registry->registerHandler(GraphNavigatorInterface::DIRECTION_DESERIALIZATION, DateTime::class, 'xml', $callable);
            })
            ->setPropertyNamingStrategy(
                new IdenticalPropertyNamingStrategy(),
            )
            ->setSerializationContextFactory(function (): Context {
                return SerializationContext::create()
                    ->addExclusionStrategy(new CXmlExclusionStrategy());
            })
            ->build();

        return new self($jmsSerializer);
    }

    public function deserialize(string $xml): CXml
    {
        $dtdUri = 'http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd';
        if (1 === preg_match('/<!doctype.+"(.+)"[^>]*>/i', $xml, $matches)) {
            $dtdUri = $matches[1];

            // remove doctype from xml (if exists), as it would throw a JMS\Serializer\Exception\InvalidArgumentException
            $xml = preg_replace('/<!doctype[^>]+?>/i', '', $xml);
        }

        if ('' === trim($xml ?? '')) {
            throw new RuntimeException('Cannot deserialize empty string');
        }

        /** @var CXml $cXml */
        $cXml = $this->jmsSerializer->deserialize($xml, CXml::class, 'xml');
        $cXml->setDtdUri($dtdUri);

        return $cXml;
    }

    public function serialize(CXml|Index $cxml): string
    {
        $xml = $this->jmsSerializer->serialize($cxml, 'xml');

        $nodeName = $cxml instanceof Index ? 'Index' : 'cXML';

        $docType = sprintf('<!DOCTYPE %s SYSTEM "%s">', $nodeName, $cxml->dtdUri);

        $xmlPrefix = '<?xml version="1.0" encoding="UTF-8"?>';

        // add doctype, as it is mandatory in cXML
        return str_replace($xmlPrefix, $xmlPrefix . $docType, $xml);
    }
}
