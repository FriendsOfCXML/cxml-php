<?php

namespace Mathielen\CXml;

use Assert\Assertion;
use Mathielen\CXml\Exception\CXmlException;
use Mathielen\CXml\Model\Credential;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\Message;
use Mathielen\CXml\Model\MessageInterface;
use Mathielen\CXml\Model\Party;
use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\Request;
use Mathielen\CXml\Model\RequestInterface;
use Mathielen\CXml\Model\Response;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Model\Status;
use Mathielen\CXml\Payload\DefaultPayloadIdentityFactory;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;

class Builder
{
    private PayloadIdentityFactoryInterface $payloadIdentityFactory;

    private ?PayloadInterface $payload = null;
    private ?Credential $from = null;
    private ?Credential $to = null;
    private ?Credential $sender = null;
    private ?string $senderUserAgent = null;
    private ?Status $status = null;

    private function __construct(PayloadIdentityFactoryInterface $payloadIdentityFactory = null)
    {
        $this->payloadIdentityFactory = $payloadIdentityFactory ?? new DefaultPayloadIdentityFactory();
    }

    public static function create(PayloadIdentityFactoryInterface $payloadIdentityFactory = null)
    {
        return new self($payloadIdentityFactory);
    }

    public function payload(PayloadInterface $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function status(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function sender(Credential $sender, string $userAgent): self
    {
        Assertion::notNull($sender->getSharedSecret(), "Sender must have a shared secret set");

        $this->sender = $sender;
        $this->senderUserAgent = $userAgent;

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
    public function build(): CXml
    {
        switch (true) {

            case $this->payload instanceof RequestInterface:
                $cXml = CXml::forRequest(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Request($this->payload, $this->status),
                    $this->buildHeader()
                );
                break;

            case $this->payload instanceof MessageInterface:
                $cXml = CXml::forMessage(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Message($this->payload, $this->status),
                    $this->buildHeader()
                );
                break;

            case $this->payload instanceof ResponseInterface:
                $cXml = CXml::forResponse(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Response($this->payload, $this->status),
                );
                break;

			default:
				//simple status ping-pong response
				if ($this->status) {
					$cXml = CXml::forResponse(
						$this->payloadIdentityFactory->newPayloadIdentity(),
						new Response(null, $this->status),
					);

					break;
				}

				throw new CXmlException("Neither payload (Request, Message, Response) was set.");
        }

        return $cXml;
    }
}
