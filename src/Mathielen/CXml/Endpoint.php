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
    private SerializerInterface $serializer;
    private Processor $processor;

    public function __construct(
        DtdValidator $messageValidator,
        Processor $processor,
        SerializerInterface $serializer = null
    ) {
        $this->dtdValidator = $messageValidator;
        $this->processor = $processor;
        $this->serializer = $serializer ?? self::buildSerializer();
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

    /**
     * @throws Exception\CXmlException
     */
    public function processStringAsCXml(string $xml): ?CXml
    {
        //validate
        $this->dtdValidator->validateAgainstDtd($xml);

        try {
            //deserialize
            $cxml = $this->serializer->deserialize($xml, CXml::class, 'xml');
        } catch (\RuntimeException $e) {
            throw new InvalidCxmlException("Error while deserializing xml: ".$e->getMessage(), $xml, $e);
        }

        //process
        return $this->processor->process($cxml);
    }

    public function toString(CXml $xml): string
    {
        $serializer = Endpoint::buildSerializer();
        return $serializer->serialize($xml, 'xml');
    }
}
