<?php

namespace Mathielen\CXml;

use Mathielen\CXml\Model\Credential;
use Mathielen\CXml\Model\Party;
use Mathielen\CXml\Model\PayloadIdentity;
use Mathielen\CXml\Model\Request\PunchOutSetupRequest;
use Mathielen\CXml\Party\PartyProviderInterface;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase implements PayloadIdentityFactoryInterface
{
    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity('123456', new \DateTime('2000-01-01'));
    }

    public function testSimpleRequest(): void
    {
        $sender = new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            "User Agent"
        );

        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com')
        );
        $to = new Party(
            new Credential('DUNS', '012345678')
        );
        $request = new PunchOutSetupRequest();

        $cxml = $this->sut->createRequest(
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
