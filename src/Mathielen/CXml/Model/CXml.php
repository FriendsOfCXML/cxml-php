<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\XmlRoot("cXML")
 */
class CXml
{
    /**
     * @Ser\XmlAttribute()
     * @Ser\SerializedName("payloadID")
     */
    private string $payloadId;

    /**
     * @Ser\XmlAttribute()
     */
    private \DateTime $timestamp;

    /**
     * @Ser\SerializedName("Header")
     */
    private ?Header $header;

    /**
     * @Ser\SerializedName("Request")
     */
    private ?Request $request;

    /**
     * @Ser\SerializedName("Response")
     */
    private ?Response $response;

    /**
     * @Ser\SerializedName("Message")
     */
    private ?Message $message;

    private function __construct(
        string $payloadId,
        \DateTime $timestamp,
        ?Request $request,
        ?Response $response = null,
        ?Message $message = null,
        ?Header $header = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->header = $header;
        $this->payloadId = $payloadId;
        $this->timestamp = $timestamp;
        $this->message = $message;
    }

    public static function forMessage(PayloadIdentity $payloadIdentity, Message $message, Header $header): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, null, $message, $header);
    }

    public static function forRequest(PayloadIdentity $payloadIdentity, Request $request, Header $header): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), $request, null, null, $header);
    }

    public static function forResponse(PayloadIdentity $payloadIdentity, Response $response): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, $response);
    }

    public function getPayloadId(): string
    {
        return $this->payloadId;
    }

    public function getTimestamp(): \DateTime
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
}
