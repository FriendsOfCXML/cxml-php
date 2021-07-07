<?php

namespace Mathielen\CXml\Model;

use Mathielen\CXml\Endpoint;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;
use PHPUnit\Framework\TestCase;
use Mathielen\CXml\Builder;
use Mathielen\CXml\Model\Request\StatusUpdateRequest;

class StatusUpdateRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
    public function testMinimumExample(): void
    {
        $from = new Credential(
            'NetworkId',
            'AN00000123'
        );
        $to = new Credential(
            'NetworkId',
            'AN00000456'
        );
        $sender = new Credential(
            'NetworkId',
            'AN00000123',
            'abracadabra'
        );

        $statusUpdateRequest = new StatusUpdateRequest(
            new Status(200, 'OK', 'Forwarded to supplier', 'en-US'),
            '0c300508b7863dcclb_14999'
        );

        $cxml = Builder::create('en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender, 'Supplier’s Super Order Processor')
            ->payload($statusUpdateRequest)
            ->build();

        $xml = Endpoint::serialize($cxml);

        $this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/StatusUpdateRequest.xml', $xml);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '0c30050@supplierorg.com',
            new \DateTime('2021-01-08T23:00:06-08:00')
        );
    }
}
