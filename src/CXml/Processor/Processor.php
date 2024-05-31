<?php

declare(strict_types=1);

namespace CXml\Processor;

use CXml\Builder;
use CXml\Context;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlConflictException;
use CXml\Exception\CXmlException;
use CXml\Exception\CXmlExpectationFailedException;
use CXml\Exception\CXmlNotAcceptableException;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Exception\CXmlPreconditionFailedException;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistryInterface;
use CXml\Model\CXml;
use CXml\Model\Message\Message;
use CXml\Model\PayloadInterface;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Status;
use CXml\Processor\Exception\CXmlProcessException;

class Processor
{
    // according to cXML reference document
    private static array $exceptionMapping = [
        CXmlAuthenticationInvalidException::class => 401,
        CXmlNotAcceptableException::class => 406,
        CXmlConflictException::class => 409,
        CXmlPreconditionFailedException::class => 412,
        CXmlExpectationFailedException::class => 417,
        CXmlNotImplementedException::class => 450,
    ];

    private static array $exceptionCodeMapping = [
        // cxml
        450 => 'Not Implemented',

        // http - shamelessly copied from Symfony\Component\HttpFoundation\Response
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => "I'm a teapot",                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

    public function __construct(private readonly HeaderProcessor $headerProcessor, private readonly HandlerRegistryInterface $handlerRegistry, private readonly Builder $builder)
    {
    }

    /**
     * @throws CXmlException
     */
    public function process(CXml $cxml, Context $context = null): ?CXml
    {
        $context ??= Context::create();
        $context->setCXml($cxml);

        $request = $cxml->getRequest();
        if ($request instanceof Request) {
            return $this->processRequest($request, $context);
        }

        $response = $cxml->getResponse();
        if ($response instanceof Response) {
            $this->processResponse($response, $context);

            return null;
        }

        $message = $cxml->getMessage();
        if ($message instanceof Message) {
            $this->processMessage($message, $context);

            return null;
        }

        throw new CXmlException('Invalid CXml. Either request, response or message must be given.');
    }

    private function getHandlerForPayload(PayloadInterface $payload): HandlerInterface
    {
        $handlerId = (new \ReflectionClass($payload))->getShortName();

        return $this->handlerRegistry->get($handlerId);
    }

    /**
     * @throws CXmlProcessException
     * @throws CXmlException
     */
    private function processMessage(Message $message, Context $context): void
    {
        $header = $context->getCXml() instanceof CXml ? $context->getCXml()->getHeader() : null;
        if (!$header instanceof \CXml\Model\Header) {
            throw new CXmlException('Invalid CXml. Header is mandatory for message.');
        }

        try {
            $this->headerProcessor->process($header, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new CXmlProcessException($e);
        }

        $payload = $message->getPayload();
        try {
            $this->getHandlerForPayload($payload)->handle($payload, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new CXmlProcessException($e);
        }
    }

    /**
     * @throws CXmlProcessException
     * @throws CXmlException
     */
    private function processResponse(Response $response, Context $context): void
    {
        $payload = $response->getPayload();

        if (!$payload instanceof ResponsePayloadInterface) {
            return;
        }

        try {
            $this->getHandlerForPayload($payload)->handle($payload, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new CXmlProcessException($e);
        }
    }

    /**
     * @throws CXmlProcessException
     * @throws CXmlException
     */
    private function processRequest(Request $request, Context $context): CXml
    {
        $header = $context->getCXml() instanceof CXml ? $context->getCXml()->getHeader() : null;
        if (!$header instanceof \CXml\Model\Header) {
            throw new CXmlException('Invalid CXml. Header is mandatory for request.');
        }

        try {
            $this->headerProcessor->process($header, $context);
        } catch (CXmlException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new CXmlProcessException($e);
        }

        $payload = $request->getPayload();
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

    public function buildResponseForException(CXmlException $exception): CXml
    {
        $statusCode = self::$exceptionMapping[$exception::class] ?? 500;
        $statusText = self::$exceptionCodeMapping[$statusCode] ?? 'Unknown status';
        $status = new Status($statusCode, $statusText, $exception->getMessage());

        return $this->builder
            ->status($status)
            ->build();
    }
}
