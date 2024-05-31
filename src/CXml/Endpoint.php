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
     * @throws CXmlException
     * @throws CXmlInvalidException
     */
    public function parseAndProcessStringAsCXml(string $xml, Context $context = null): ?CXml
    {
        $this->logger->info('Processing incoming CXml message', ['xml' => $xml]);

        // validate
        try {
            $this->dtdValidator->validateAgainstDtd($xml);
        } catch (CXmlInvalidException $cXmlInvalidException) {
            $this->logger->error('Incoming CXml was invalid (via DTD)', ['xml' => $xml]);

            throw $cXmlInvalidException;
        }

        // deserialize
        try {
            $cxml = $this->serializer->deserialize($xml);
        } catch (\RuntimeException $runtimeException) {
            $this->logger->error('Error while deserializing xml to CXml: '.$runtimeException->getMessage(), ['xml' => $xml]);

            throw new CXmlInvalidException('Error while deserializing xml: '.$runtimeException->getMessage(), $xml, $runtimeException);
        }

        // process
        try {
            $result = $this->processor->process($cxml, $context);
        } catch (CXmlException $cXmlException) {
            $this->logger->error('Error while processing valid CXml: '.$cXmlException->getMessage(), ['xml' => $xml]);

            throw $cXmlException;
        }

        $this->logger->info('Success after processing incoming CXml message', ['xml' => $xml]);

        return $result;
    }
}
