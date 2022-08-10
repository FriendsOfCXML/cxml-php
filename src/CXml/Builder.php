<?php

namespace CXml;

use Assert\Assertion;
use CXml\Exception\CXmlException;
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

class Builder
{
	private PayloadIdentityFactoryInterface $payloadIdentityFactory;

	private ?PayloadInterface $payload = null;
	private Credential $from;
	private Credential $to;
	private Credential $sender;
	private ?string $senderUserAgent;
	private ?Status $status = null;
	private ?string $locale;

	private function __construct(string $senderUserAgent, ?string $locale = null, PayloadIdentityFactoryInterface $payloadIdentityFactory = null)
	{
		$this->locale = $locale;
		$this->payloadIdentityFactory = $payloadIdentityFactory ?? new DefaultPayloadIdentityFactory();
		$this->senderUserAgent = $senderUserAgent;
	}

	public static function create(string $senderUserAgent = 'cxml-php UserAgent', string $locale = null, PayloadIdentityFactoryInterface $payloadIdentityFactory = null): self
	{
		return new self($senderUserAgent, $locale, $payloadIdentityFactory);
	}

	public function payload(?PayloadInterface $payload = null): self
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
		Assertion::notNull($this->from, "No 'from' has been set. Necessary for build a Header.");
		Assertion::notNull($this->to, "No 'to' has been set. Necessary for build a Header.");
		Assertion::notNull($this->sender, "No 'sender' has been set. Necessary for build a Header.");

		return new Header(
			new Party($this->from),
			new Party($this->to),
			new Party($this->sender, $this->senderUserAgent)
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
					$this->locale
				);
				break;

			case $this->payload instanceof MessagePayloadInterface:
				/** @noinspection PhpParamsInspection */
				$cXml = CXml::forMessage(
					$this->payloadIdentityFactory->newPayloadIdentity(),
					new Message($this->payload, $this->status),
					$this->buildHeader(),
					$this->locale
				);
				break;

			case $this->payload instanceof ResponsePayloadInterface:
				$status = $this->status;

				// response requires a status
				if (null === $status) {
					$status = new Status(); // 200 OK
				}

				/** @noinspection PhpParamsInspection */
				$cXml = CXml::forResponse(
					$this->payloadIdentityFactory->newPayloadIdentity(),
					new Response($status, $this->payload),
					$this->locale
				);
				break;

			default:
				// simple status ping-pong response
				if ($this->status) {
					$cXml = CXml::forResponse(
						$this->payloadIdentityFactory->newPayloadIdentity(),
						new Response($this->status),
						$this->locale
					);

					break;
				}
		}

		if (!isset($cXml)) {
			throw new CXmlException('Neither payload (Request, Message, Response) was set.');
		}

		return $cXml;
	}
}
