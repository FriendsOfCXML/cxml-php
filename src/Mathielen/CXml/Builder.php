<?php

namespace Mathielen\CXml;

use Assert\Assertion;
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

    private PayloadInterface $payload;
    private Credential $from;
    private Credential $to;
    private Credential $sender;
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

    public function sender(Credential $sender, string $userAgent = null): self
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
        Assertion::notNull($this->from, "No 'from' has been set");
        Assertion::notNull($this->to, "No 'to' has been set");
        Assertion::notNull($this->sender, "No 'sender' has been set");

        return new Header(
            new Party($this->from),
            new Party($this->to),
            new Party($this->sender, $this->senderUserAgent)
        );
    }

    public function build(): CXml
    {
        Assertion::notNull($this->payload, "No 'payload' has been set");

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
            default:
                $cXml = CXml::forResponse(
                    $this->payloadIdentityFactory->newPayloadIdentity(),
                    new Response($this->payload, $this->status),
                );
                break;
        }

        return $cXml;
    }
}
