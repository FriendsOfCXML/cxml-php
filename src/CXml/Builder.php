<?php

declare(strict_types=1);

namespace CXml;

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
use LogicException;

class Builder
{
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
}
