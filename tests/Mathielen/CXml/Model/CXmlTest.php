<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class CXmlTest extends TestCase
{
    private static function buildSerializer(): SerializerInterface
    {
        return SerializerBuilder::create()
            ->build();
    }

    public function testSimpleRequest(): void
    {
        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com')
        );
        $to = new Party(
            new Credential('DUNS', '012345678')
        );
        $sender = new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            "Network Hub 1.1"
        );
        $request = new PunchOutSetupRequest();

        $header = new Header(
            $from,
            $to,
            $sender
        );

        $msg = CXml::forRequest(
            new PayloadIdentity('payload-id', new \DateTime('2000-01-01')),
            $request,
            $header
        );

        $actualXml =
            self::buildSerializer()
                ->serialize($msg, 'xml');

        $expectedXml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<cXML payloadID="payload-id" timestamp="2000-01-01T00:00:00+00:00">
<Header>
 <From>
 <Credential domain="AribaNetworkUserId">
 <Identity>admin@acme.com</Identity>
 </Credential>
 </From>
 <To>
 <Credential domain="DUNS">
 <Identity>012345678</Identity>
 </Credential>
 </To>
 <Sender>
 <Credential domain="AribaNetworkUserId">
 <Identity>sysadmin@buyer.com</Identity>
 <SharedSecret>abracadabra</SharedSecret>
 </Credential>
 <UserAgent>Network Hub 1.1</UserAgent>
 </Sender>
</Header>
<Request />
</cXML>
EOT;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testSimpleResponse(): void
    {
        $msg = CXml::forResponse(
            new PayloadIdentity('978979621537--4882920031100014936@206.251.25.169', new \DateTime('2001-01-08T10:47:01-08:00')),
            new StatusResponse(
                new Status(200, "OK", "Ping Response CXml")
            )
        );

        $actualXml =
            self::buildSerializer()
            ->serialize($msg, 'xml');

        $expectedXml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<cXML timestamp="2001-01-08T10:47:01-08:00"
payloadID="978979621537--4882920031100014936@206.251.25.169">
 <Response>
 <Status code="200" text="OK">Ping Response CXml</Status>
 </Response>
</cXML>
EOT;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }
}
