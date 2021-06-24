<?php

namespace Mathielen\CXml\Processor;

use Mathielen\CXml\Exception\CXmlException;
use Mathielen\CXml\Handler\HandlerRegistryInterface;
use Mathielen\CXml\Model;
use Mathielen\CXml\Model\CXml;

class Processor
{
    private HeaderProcessor $headerProcessor;
    private HandlerRegistryInterface $handlerRegistry;

    public function __construct(HeaderProcessor $requestProcessor, HandlerRegistryInterface $handlerRepository)
    {
        $this->headerProcessor = $requestProcessor;
        $this->handlerRegistry = $handlerRepository;
    }

    /**
     * @throws CXmlException
     */
    public function process(CXml $cxml): ?CXml
    {
        if ($request = $cxml->getRequest()) {
            $header = $cxml->getHeader();
            if (!$header) {
                throw new CXmlException("Invalid CXml. Header is mandatory for request message.");
            }

            $this->headerProcessor->process($header);

            return $this->handlerRegistry->get(get_class($request))->handle($request, $header);
        }

        if ($response = $cxml->getResponse()) {
            $this->handlerRegistry->get(get_class($response))->handle($response);

            return null;
        }

        if ($message = $cxml->getMessage()) {
            $this->handlerRegistry->get(get_class($message))->handle($message);

            return null;
        }

        throw new CXmlException("Invalid CXml. Either request, response or message must be given.");
    }
}
