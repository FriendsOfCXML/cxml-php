<?php

namespace Mathielen\CXml;

use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Mathielen\CXml\Jms\JmsEventSubscriber;
use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Processor\Processor;
use Mathielen\CXml\Validation\DtdValidator;
use Mathielen\CXml\Validation\Exception\InvalidCxmlException;

class Endpoint
{
    private DtdValidator $dtdValidator;
    private Processor $processor;

    public function __construct(
        DtdValidator $messageValidator,
        Processor $processor
    ) {
        $this->dtdValidator = $messageValidator;
        $this->processor = $processor;
    }

    public static function buildSerializer(): SerializerInterface
    {
        return SerializerBuilder::create()
            ->configureListeners(function (EventDispatcherInterface $dispatcher) {
                $dispatcher->addSubscriber(new JmsEventSubscriber());
            })
            ->setPropertyNamingStrategy(
                new IdenticalPropertyNamingStrategy()
            )
            ->build();
    }

    public static function deserialize(string $xml): CXml
    {
        return self::buildSerializer()->deserialize($xml, CXml::class, 'xml');
    }

    public static function serialize(CXml $cxml): string
    {
        return self::buildSerializer()->serialize($cxml, 'xml');
    }

    /**
     * @throws Exception\CXmlException
     */
    public function processStringAsCXml(string $xml): ?CXml
    {
        //validate
        $this->dtdValidator->validateAgainstDtd($xml);

        try {
            //deserialize
            $cxml = self::deserialize($xml);
        } catch (\RuntimeException $e) {
            throw new InvalidCxmlException("Error while deserializing xml: ".$e->getMessage(), $xml, $e);
        }

        //process
        return $this->processor->process($cxml);
    }
}
