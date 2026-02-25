<?php

declare(strict_types=1);

namespace CXml\Processor;

use CXml\Builder;
use CXml\Context;
use CXml\Exception\CXmlException;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistryInterface;
use CXml\Model\CXml;
use CXml\Model\Header;
use CXml\Model\Message\Message;
use CXml\Model\PayloadInterface;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Status;
use CXml\Processor\Event\CXmlProcessEvent;
use CXml\Processor\Exception\CXmlProcessException;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;
use Throwable;

class Processor
{
    public function __construct(private readonly HeaderProcessor $headerProcessor, private readonly HandlerRegistryInterface $handlerRegistry, private readonly Builder $builder, private readonly ?EventDispatcherInterface $eventDispatcher = null)
    {
    }

    /**
     * @throws CXmlException
     */
    public function process(CXml $cxml, ?Context $context = null): ?CXml
    {
        $context ??= Context::create();
        $context->setCXml($cxml);

        $this->eventDispatcher?->dispatch(new CXmlProcessEvent($cxml, $context));

        $request = $cxml->request;
        if ($request instanceof Request) {
            return $this->processRequest($request, $context);
        }

        $response = $cxml->response;
        if ($response instanceof Response) {
            $this->processResponse($response, $context);

            return null;
        }

        $message = $cxml->message;
        if ($message instanceof Message) {
            $this->processMessage($message, $context);

            return null;
        }

        throw new CXmlException('Invalid CXml. Either request, response or message must be given.');
    }

    private function getHandlerForPayload(PayloadInterface $payload): HandlerInterface
    {
        $handlerId = (new ReflectionClass($payload))->getShortName();

        return $this->handlerRegistry->get($handlerId);
    }

    /**
     * @throws CXmlProcessException
     * @throws CXmlException
     */
    private function processMessage(Message $message, Context $context): void
    {
        $header = $context->getCXml() instanceof CXml ? $context->getCXml()->header : null;
        if (!$header instanceof Header) {
            throw new CXmlException('Invalid CXml. Header is mandatory for message.');
        }

        try {
            $this->headerProcessor->process($header, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CXmlProcessException($e);
        }

        $payload = $message->payload;
        try {
            $this->getHandlerForPayload($payload)->handle($payload, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CXmlProcessException($e);
        }
    }

    /**
     * @throws CXmlProcessException
     * @throws CXmlException
     */
    private function processResponse(Response $response, Context $context): void
    {
        $payload = $response->payload ?? null;

        if (!$payload instanceof ResponsePayloadInterface) {
            return;
        }

        try {
            $this->getHandlerForPayload($payload)->handle($payload, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CXmlProcessException($e);
        }
    }

    /**
     * @throws CXmlProcessException
     * @throws CXmlException
     */
    private function processRequest(Request $request, Context $context): CXml
    {
        $header = $context->getCXml() instanceof CXml ? $context->getCXml()->header : null;
        if (!$header instanceof Header) {
            throw new CXmlException('Invalid CXml. Header is mandatory for request.');
        }

        try {
            $this->headerProcessor->process($header, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new CXmlProcessException($e);
        }

        $payload = $request->payload;
        $handler = $this->getHandlerForPayload($payload);

        $response = $handler->handle($payload, $context);

        // if no response was returned, set an implicit 200/OK
        if (!$response instanceof ResponsePayloadInterface) {
            $this->builder->status(new Status(
                200,
                'OK',
            ));
        }

        return $this->builder
            ->payload($response)
            ->build();
    }
}
