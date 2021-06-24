<?php

namespace Mathielen\CXml;

use Mathielen\CXml\Model\Credential;
use Mathielen\CXml\Model\Party;
use Mathielen\CXml\Model\PayloadIdentity;
use Mathielen\CXml\Model\PunchOutSetupRequest;
use Mathielen\CXml\Model\StatusResponse;
use Mathielen\CXml\Party\PartyProviderInterface;
use Mathielen\CXml\TimeLocation\TimeLocationProviderInterface;
use PHPUnit\Framework\MockObject\Builder\Identity;
use PHPUnit\Framework\TestCase;

class CXmlBrokerTest extends TestCase implements TimeLocationProviderInterface, PartyProviderInterface
{
    private CXmlBroker $sut;

    public function setUp(): void
    {
        $this->sut = new CXmlBroker(
            $this,
            $this
        );
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity('123456', new \DateTime('2000-01-01'));
    }

    public function getOwnParty(): Party
    {
        return new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            "User Agent"
        );
    }

    public function testSimpleRequest(): void
    {
        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com')
        );
        $to = new Party(
            new Credential('DUNS', '012345678')
        );
        $request = new PunchOutSetupRequest();

        $message = $this->sut->createRequestMessage(
            $from,
            $to,
            $request
        );

        //TODO compare?
    }

    public function testSimpleResponse(): void
    {
        //$this->sut->createResponseMessageForRequestMessage(new PunchOutSetupResponse());

        //TODO compare?
    }
}
