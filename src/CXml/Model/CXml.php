<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Message\Message;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot('cXML')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['header', 'message', 'request', 'response'])]
class CXml implements \Stringable
{
    final public const DEPLOYMENT_TEST = 'test';

    final public const DEPLOYMENT_PROD = 'production';

    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('payloadID')]
        private readonly string $payloadId,
        #[Serializer\XmlAttribute]
        private readonly \DateTimeInterface $timestamp,
        #[Serializer\SerializedName('Request')]
        private readonly ?Request $request = null,
        #[Serializer\SerializedName('Response')]
        private readonly ?Response $response = null,
        #[Serializer\SerializedName('Message')]
        private readonly ?Message $message = null,
        #[Serializer\SerializedName('Header')]
        private readonly ?Header $header = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        private readonly ?string $lang = null,
    ) {
    }

    public static function forMessage(PayloadIdentity $payloadIdentity, Message $message, Header $header, string $lang = null): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, null, $message, $header, $lang);
    }

    public static function forRequest(PayloadIdentity $payloadIdentity, Request $request, Header $header, string $lang = null): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), $request, null, null, $header, $lang);
    }

    public static function forResponse(PayloadIdentity $payloadIdentity, Response $response, string $lang = null): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, $response, null, null, $lang);
    }

    public function getPayloadId(): string
    {
        return $this->payloadId;
    }

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getHeader(): ?Header
    {
        return $this->header;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function __toString(): string
    {
        $wrapper = $this->message ?? $this->request ?? $this->response;

        $shortName = 'undefined';
        if (null !== $wrapper) {
            $payload = $wrapper->getPayload();

            if (null !== $payload) {
                $shortName = (new \ReflectionClass($payload))->getShortName();
            } else {
                $shortName = (new \ReflectionClass($wrapper))->getShortName();
            }
        }

        return $shortName . '_' . $this->payloadId;
    }

    public function getStatus(): ?Status
    {
        if ($this->request instanceof Request) {
            return $this->request->getStatus();
        }

        if ($this->message instanceof Message) {
            return $this->message->getStatus();
        }

        if ($this->response instanceof Response) {
            return $this->response->getStatus();
        }

        return null;
    }
}
