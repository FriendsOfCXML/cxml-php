<?php

namespace Mathielen\CXml\Processor;

use Mathielen\CXml\Builder;
use Mathielen\CXml\Exception\CXmlException;
use Mathielen\CXml\Handler\HandlerInterface;
use Mathielen\CXml\Handler\HandlerRegistryInterface;
use Mathielen\CXml\Model;
use Mathielen\CXml\Model\CXml;

class Processor
{
    private HeaderProcessor $headerProcessor;
    private HandlerRegistryInterface $handlerRegistry;
    private Builder $builder;

    public function __construct(
        HeaderProcessor $requestProcessor,
        HandlerRegistryInterface $handlerRepository,
        Builder $builder
    ) {
        $this->headerProcessor = $requestProcessor;
        $this->handlerRegistry = $handlerRepository;
        $this->builder = $builder;
    }

    /**
     * @throws CXmlException
     * @throws \Mathielen\CXml\Exception\CXmlCredentialInvalidException
     * @throws \ReflectionException
     */
    public function process(CXml $cxml): ?CXml
    {
        if ($request = $cxml->getRequest()) {
            $header = $cxml->getHeader();
            if (!$header) {
                throw new CXmlException("Invalid CXml. Header is mandatory for request message.");
            }

            return $this->processRequest($request, $header);
        }

        if ($response = $cxml->getResponse()) {
            $this->processResponse($response);

            return null;
        }

        if ($message = $cxml->getMessage()) {
            $this->processMessage($message);

            return null;
        }

        throw new CXmlException("Invalid CXml. Either request, response or message must be given.");
    }

    private function getHandlerForPayload(Model\PayloadInterface $payload): HandlerInterface
    {
        $handlerId = (new \ReflectionClass($payload))->getShortName();

        return $this->handlerRegistry->get($handlerId);
    }

    private function processMessage(Model\Message $message): void
    {
        $payload = $message->getPayload();
        $this->getHandlerForPayload($payload)->handle($payload);
    }

    private function processResponse(Model\Response $response): void
    {
        $payload = $response->getPayload();
        $this->getHandlerForPayload($payload)->handle($payload);
    }

    /**
     * @throws CXmlException
     * @throws \Mathielen\CXml\Exception\CXmlCredentialInvalidException
     * @throws \ReflectionException
     */
    private function processRequest(Model\Request $request, Model\Header $header): CXml
    {
        $this->headerProcessor->process($header);

        $payload = $request->getPayload();
        $handler = $this->getHandlerForPayload($payload);

        $response = $handler->handle($payload, $header);
        if (!$response) {
            throw new CXmlException("A request expects a response. None returned from handler ".get_class($handler).".");
        }

        return $this->builder
            ->payload($response)
            ->build();
    }
}
