<?php

declare(strict_types=1);

namespace CXml;

use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlConflictException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Exception\CXmlException;
use CXml\Exception\CXmlExpectationFailedException;
use CXml\Exception\CXmlNotAcceptableException;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Exception\CXmlPreconditionFailedException;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Header;
use CXml\Model\Message\Message;
use CXml\Model\Message\MessagePayloadInterface;
use CXml\Model\Party;
use CXml\Model\PayloadInterface;
use CXml\Model\Request\Request;
use CXml\Model\Request\RequestPayloadInterface;
use CXml\Model\Response\Response;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Status;
use CXml\Payload\DefaultPayloadIdentityFactory;
use CXml\Payload\PayloadIdentityFactoryInterface;
use LogicException;

class Builder
{
    // according to cXML reference document
    private static array $exceptionMapping = [
        CXmlAuthenticationInvalidException::class => 401,
        CXmlNotAcceptableException::class => 406,
        CXmlConflictException::class => 409,
        CXmlPreconditionFailedException::class => 412,
        CXmlExpectationFailedException::class => 417,
        CXmlCredentialInvalidException::class => 417,
        CXmlNotImplementedException::class => 450,
    ];

    // TODO create enum for this?
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

    private readonly PayloadIdentityFactoryInterface $payloadIdentityFactory;

    private ?PayloadInterface $payload = null;

    private Credential $from;

    private Credential $to;

    private Credential $sender;

    private ?Status $status = null;

    private function __construct(private ?string $senderUserAgent, private readonly ?string $locale = null, PayloadIdentityFactoryInterface $payloadIdentityFactory = null)
    {
        $this->payloadIdentityFactory = $payloadIdentityFactory ?? new DefaultPayloadIdentityFactory();
    }

    public static function create(string $senderUserAgent = 'cxml-php UserAgent', string $locale = null, PayloadIdentityFactoryInterface $payloadIdentityFactory = null): self
    {
        return new self($senderUserAgent, $locale, $payloadIdentityFactory);
    }

    public function payload(PayloadInterface $payload = null): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function status(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function sender(Credential $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function setSenderUserAgent(?string $senderUserAgent): self
    {
        $this->senderUserAgent = $senderUserAgent;

        return $this;
    }

    public function from(Credential $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function to(Credential $to): self
    {
        $this->to = $to;

        return $this;
    }

    private function buildHeader(): Header
    {
        if (!isset($this->from)) {
            throw new LogicException("No 'from' has been set. Necessary for building a header.");
        }

        if (!isset($this->to)) {
            throw new LogicException("No 'to' has been set. Necessary for building a header.");
        }

        if (!isset($this->sender)) {
            throw new LogicException("No 'sender' has been set. Necessary for building a header.");
        }

        return new Header(
            new Party($this->from),
            new Party($this->to),
            new Party($this->sender, $this->senderUserAgent),
        );
    }

    /**
     * @throws CXmlException
     */
    public function build(string $deploymentMode = null): CXml
    {
        switch (true) {
            case $this->payload instanceof RequestPayloadInterface:
                /** @noinspection PhpParamsInspection */
                $cXml = CXml::forRequest(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Request($this->payload, $this->status, null, $deploymentMode),
                    $this->buildHeader(),
                    $this->locale,
                );
                break;

            case $this->payload instanceof MessagePayloadInterface:
                /** @noinspection PhpParamsInspection */
                $cXml = CXml::forMessage(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Message($this->payload, $this->status, null, $deploymentMode),
                    $this->buildHeader(),
                    $this->locale,
                );
                break;

            case $this->payload instanceof ResponsePayloadInterface:
                $status = $this->status;

                // response requires a status
                if (!$status instanceof Status) {
                    $status = new Status(); // 200 OK
                }

                /** @noinspection PhpParamsInspection */
                $cXml = CXml::forResponse(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Response($status, $this->payload),
                    $this->locale,
                );
                break;

            default:
                // simple status ping-pong response
                if ($this->status instanceof Status) {
                    $cXml = CXml::forResponse(
                        $this->payloadIdentityFactory->newPayloadIdentity(),
                        new Response($this->status),
                        $this->locale,
                    );

                    break;
                }
        }

        if (!isset($cXml)) {
            throw new CXmlException('Neither payload (Request, Message, Response) was set.');
        }

        return $cXml;
    }

    /**
     * @throws CXmlException
     */
    public function buildResponseForException(CXmlException $exception): CXml
    {
        $statusCode = self::$exceptionMapping[$exception::class] ?? 500;
        $statusText = self::$exceptionCodeMapping[$statusCode] ?? 'Unknown status';
        $status = new Status($statusCode, $statusText, $exception->getMessage());

        return $this
            ->status($status)
            ->build();
    }
}
