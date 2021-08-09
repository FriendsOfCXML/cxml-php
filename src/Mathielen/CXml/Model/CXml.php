<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\XmlRoot("cXML")
 */
class CXml
{
	/**
	 * @Ser\XmlAttribute(namespace="http://www.w3.org/XML/1998/namespace")
	 */
	private ?string $lang = null;

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
	private ?Header $header = null;

	/**
	 * @Ser\SerializedName("Request")
	 */
	private ?Request $request = null;

	/**
	 * @Ser\SerializedName("Response")
	 */
	private ?Response $response = null;

	/**
	 * @Ser\SerializedName("Message")
	 */
	private ?Message $message = null;

	private function __construct(
		string $payloadId,
		\DateTime $timestamp,
		?Request $request,
		?Response $response = null,
		?Message $message = null,
		?Header $header = null,
		?string $lang = null
	) {
		$this->request = $request;
		$this->response = $response;
		$this->header = $header;
		$this->payloadId = $payloadId;
		$this->timestamp = $timestamp;
		$this->message = $message;
		$this->lang = $lang;
	}

	public static function forMessage(PayloadIdentity $payloadIdentity, Message $message, Header $header, ?string $lang = null): self
	{
		return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, null, $message, $header, $lang);
	}

	public static function forRequest(PayloadIdentity $payloadIdentity, Request $request, Header $header, ?string $lang = null): self
	{
		return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), $request, null, null, $header, $lang);
	}

	public static function forResponse(PayloadIdentity $payloadIdentity, Response $response, ?string $lang = null): self
	{
		return new self($payloadIdentity->getPayloadId(), $payloadIdentity->getTimestamp(), null, $response, null, null, $lang);
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
