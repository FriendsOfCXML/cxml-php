<?php

namespace CXml\Model;

use CXml\Model\Message\Message;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot('cXML')]
class CXml
{
    public const DEPLOYMENT_TEST = 'test';
    public const DEPLOYMENT_PROD = 'production';

    #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
    private ?string $lang = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('payloadID')]
    private string $payloadId;

    #[Serializer\XmlAttribute]
    private \DateTimeInterface $timestamp;

    #[Serializer\SerializedName('Header')]
    private ?Header $header = null;

    #[Serializer\SerializedName('Request')]
    private ?Request $request = null;

    #[Serializer\SerializedName('Response')]
    private ?Response $response = null;

    #[Serializer\SerializedName('Message')]
    private ?Message $message = null;

    protected function __construct(
        string $payloadId,
        \DateTimeInterface $timestamp,
        ?Request $request,
        Response $response = null,
        Message $message = null,
        Header $header = null,
        string $lang = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->header = $header;
        $this->payloadId = $payloadId;
        $this->timestamp = $timestamp;
        $this->message = $message;
        $this->lang = $lang;
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
        if ($wrapper) {
            $payload = $wrapper->getPayload();

            if ($payload) {
                $shortName = (new \ReflectionClass($payload))->getShortName();
            } else {
                $shortName = (new \ReflectionClass($wrapper))->getShortName();
            }
        }

        return $shortName.'_'.$this->payloadId;
    }

    public function getStatus(): ?Status
    {
        if ($this->request) {
            return $this->request->getStatus();
        }
        if ($this->message) {
            return $this->message->getStatus();
        }
        if ($this->response) {
            return $this->response->getStatus();
        }

        return null;
    }
}
