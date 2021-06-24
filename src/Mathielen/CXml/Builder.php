<?php

namespace Mathielen\CXml;

use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\Message;
use Mathielen\CXml\Model\MessageInterface;
use Mathielen\CXml\Model\Party;
use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Model\Request;
use Mathielen\CXml\Model\RequestInterface;
use Mathielen\CXml\Model\Response;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Party\PartyProviderInterface;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;

class Builder
{
    private PartyProviderInterface $identityProvider;
    private PayloadIdentityFactoryInterface $timeLocationProvider;

    public function __construct(
        PayloadIdentityFactoryInterface $timeLocationProvider,
        PartyProviderInterface $identityProvider
    ) {
        $this->identityProvider = $identityProvider;
        $this->timeLocationProvider = $timeLocationProvider;
    }

    public function createRequest(Party $from, Party $to, RequestInterface $request): CXml
    {
        $header = new Header(
            $from,
            $to,
            $this->identityProvider->getOwnParty()
        );

        $request = new Request(
            $request
        );

        return CXml::forRequest(
            $this->timeLocationProvider->newPayloadIdentity(),
            $request,
            $header
        );
    }

    public function createMessage(Party $from, Party $to, MessageInterface $message): CXml
    {
        $header = new Header(
            $from,
            $to,
            $this->identityProvider->getOwnParty()
        );

        $message = new Message(
            $message
        );

        return CXml::forMessage(
            $this->timeLocationProvider->newPayloadIdentity(),
            $message,
            $header
        );
    }

    public function createResponse(ResponseInterface $response): CXml
    {
        $response = new Response(
            $response
        );

        return CXml::forResponse(
            $this->timeLocationProvider->newPayloadIdentity(),
            $response
        );
    }
}
