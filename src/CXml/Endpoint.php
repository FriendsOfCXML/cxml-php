<?php

namespace CXml;

use CXml\Exception\CXmlException;
use CXml\Model\CXml;
use CXml\Processor\Processor;
use CXml\Validation\DtdValidator;
use CXml\Validation\Exception\CXmlInvalidException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Endpoint
{
    private Serializer $serializer;
    private DtdValidator $dtdValidator;
    private Processor $processor;
    private LoggerInterface $logger;

    public function __construct(
        Serializer $serializer,
        DtdValidator $messageValidator,
        Processor $processor,
        LoggerInterface $logger = null
    ) {
        $this->serializer = $serializer;
        $this->dtdValidator = $messageValidator;
        $this->processor = $processor;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws Exception\CXmlException
     */
    public function parseAndProcessStringAsCXml(string $xml, Context $context = null): ?CXml
    {
        $this->logger->info('Processing incoming CXml message', ['xml' => $xml]);

        // validate
        try {
            $this->dtdValidator->validateAgainstDtd($xml);
        } catch (CXmlInvalidException $e) {
            $this->logger->error('Incoming CXml was invalid (via DTD)', ['xml' => $xml]);

            throw $e;
        }

        // deserialize
        try {
            $cxml = $this->serializer->deserialize($xml);
        } catch (\RuntimeException $e) {
            $this->logger->error('Error while deserializing xml to CXml: '.$e->getMessage(), ['xml' => $xml]);

            throw new CXmlInvalidException('Error while deserializing xml: '.$e->getMessage(), $xml, $e);
        }

        // process
        try {
            $result = $this->processor->process($cxml, $context);
        } catch (CXmlException $e) {
            $this->logger->error('Error while processing valid CXml: '.$e->getMessage(), ['xml' => $xml]);

            throw $e;
        }

        $this->logger->info('Success after processing incoming CXml message', ['xml' => $xml]);

        return $result;
    }
}
