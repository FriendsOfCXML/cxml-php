<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Message\Message;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;
use ReflectionClass;
use Stringable;

#[Serializer\XmlRoot('cXML')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['header', 'message', 'request', 'response'])]
class CXml implements Stringable
{
    final public const DEPLOYMENT_TEST = 'test';

    final public const DEPLOYMENT_PROD = 'production';

    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('payloadID')]
        readonly public string $payloadId,
        #[Serializer\XmlAttribute]
        readonly public DateTimeInterface $timestamp,
        #[Serializer\SerializedName('Request')]
        readonly public ?Request $request = null,
        #[Serializer\SerializedName('Response')]
        readonly public ?Response $response = null,
        #[Serializer\SerializedName('Message')]
        readonly public ?Message $message = null,
        #[Serializer\SerializedName('Header')]
        readonly public ?Header $header = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        readonly public ?string $lang = null,
        #[Serializer\Exclude]
        public string $dtdUri = 'http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd',
    ) {
    }

    public function setDtdUri(string $dtdUri): void
    {
        $this->dtdUri = $dtdUri;
    }

    public static function forMessage(PayloadIdentity $payloadIdentity, Message $message, Header $header, ?string $lang = null, string $dtdUri = 'http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd'): self
    {
        return new self($payloadIdentity->payloadId, $payloadIdentity->timestamp, null, null, $message, $header, $lang, $dtdUri);
    }

    public static function forRequest(PayloadIdentity $payloadIdentity, Request $request, Header $header, ?string $lang = null, string $dtdUri = 'http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd'): self
    {
        return new self($payloadIdentity->payloadId, $payloadIdentity->timestamp, $request, null, null, $header, $lang, $dtdUri);
    }

    public static function forResponse(PayloadIdentity $payloadIdentity, Response $response, ?string $lang = null, string $dtdUri = 'http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd'): self
    {
        return new self($payloadIdentity->payloadId, $payloadIdentity->timestamp, null, $response, null, null, $lang, $dtdUri);
    }

    public function __toString(): string
    {
        $wrapper = $this->message ?? $this->request ?? $this->response;

        $shortName = 'undefined';
        if (null !== $wrapper) {
            $payload = $wrapper->payload;

            if (null !== $payload) {
                $shortName = (new ReflectionClass($payload))->getShortName();
            } else {
                $shortName = (new ReflectionClass($wrapper))->getShortName();
            }
        }

        return $shortName . '_' . $this->payloadId;
    }

    public function getStatus(): ?Status
    {
        if ($this->message instanceof Message) {
            return $this->message->status;
        }

        if ($this->response instanceof Response) {
            return $this->response->status;
        }

        return null;
    }
}
