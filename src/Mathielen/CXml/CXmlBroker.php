<?php

namespace Mathielen\CXml;

use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\Party;
use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\OrderRequest;
use Mathielen\CXml\Model\RequestInterface;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Party\PartyProvider;
use Mathielen\CXml\Party\PartyProviderInterface;
use Mathielen\CXml\TimeLocation\TimeLocationProviderInterface;

class CXmlBroker
{
    private PartyProviderInterface $identityProvider;
    private TimeLocationProviderInterface $timeLocationProvider;

    public function __construct(
        TimeLocationProviderInterface $timeLocationProvider,
        PartyProviderInterface $identityProvider
    ) {
        $this->identityProvider = $identityProvider;
        $this->timeLocationProvider = $timeLocationProvider;
    }

    public function createRequestMessage(Party $from, Party $to, RequestInterface $request): CXml
    {
        $header = new Header(
            $from,
            $to,
            $this->identityProvider->getOwnParty()
        );

        return CXml::forRequest(
            $this->timeLocationProvider->newPayloadIdentity(),
            $request,
            $header
        );
    }

    public function createResponseMessageForRequestMessage(ResponseInterface $response): CXml
    {
        return CXml::forResponse(
            $this->timeLocationProvider->newPayloadIdentity(),
            $response
        );
    }
}
