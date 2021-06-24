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
    private ?RequestInterface $request;

    /**
     * @Ser\SerializedName("Response")
     */
    private ?ResponseInterface $response;

    /**
     * @Ser\SerializedName("Message")
     */
    private ?Message $message;

    private function __construct(
        string $payloadId,
        \DateTime $timestamp,
        ?RequestInterface $request,
        ?ResponseInterface $response = null,
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

    public static function forRequest(PayloadIdentity $payloadIdentity, RequestInterface $request, Header $header): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), $request, null, null, $header);
    }

    public static function forResponse(PayloadIdentity $payloadIdentity, ResponseInterface $response): self
    {
        return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, $response);
    }
}
