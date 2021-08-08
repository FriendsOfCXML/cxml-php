<?php

namespace Mathielen\CXml;

use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Mathielen\CXml\Exception\CXmlException;
use Mathielen\CXml\Jms\JmsEventSubscriber;
use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Processor\Processor;
use Mathielen\CXml\Validation\DtdValidator;
use Mathielen\CXml\Validation\Exception\CxmlInvalidException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Endpoint
{
    private DtdValidator $dtdValidator;
    private Processor $processor;
    private LoggerInterface $logger;

    public function __construct(
        DtdValidator $messageValidator,
        Processor $processor,
        LoggerInterface $logger = null
    ) {
        $this->dtdValidator = $messageValidator;
        $this->processor = $processor;
        $this->logger = $logger ?? new NullLogger();
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
    	//remove doctype, as it would throw a JMS\Serializer\Exception\InvalidArgumentException
    	$xml = preg_replace('/<!doctype.+>/i', '', $xml);

        return self::buildSerializer()->deserialize($xml, CXml::class, 'xml');
    }

    public static function serialize(CXml $cxml): string
    {
        return self::buildSerializer()->serialize($cxml, 'xml');
    }

    /**
     * @throws Exception\CXmlException
     */
    public function parseAndProcessStringAsCXml(string $xml, Context $context = null): ?CXml
    {
        $this->logger->info("Processing incoming CXml message", ['xml'=>$xml]);

        //validate
        try {
            $this->dtdValidator->validateAgainstDtd($xml);
        } catch (CxmlInvalidException $e) {
            $this->logger->error("Incoming CXml was invalid (via DTD)", ['xml'=>$xml]);

            throw $e;
        }

        //deserialize
        try {
            $cxml = self::deserialize($xml);
        } catch (\RuntimeException $e) {
            $this->logger->error("Error while deserializing xml to CXml", ['xml'=>$xml]);

            throw new CxmlInvalidException("Error while deserializing xml: ".$e->getMessage(), $xml, $e);
        }

        //process
        try {
            $result = $this->processor->process($cxml, $context);
        } catch (CXmlException $e) {
            $this->logger->error("Error while processing valid CXml: ".$e->getMessage(), ['xml'=>$xml]);

            throw $e;
        }

        $this->logger->info("Success after processing incoming CXml message", ['xml'=>$xml]);

        return $result;
    }
}
