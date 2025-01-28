<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\CarrierIdentifier;
use CXml\Model\Credential;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\ShipNoticeHeader;
use CXml\Model\Request\ShipNoticeRequest;
use CXml\Model\ShipControl;
use CXml\Model\ShipmentIdentifier;
use CXml\Model\ShipNoticePortion;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Serializer;
use CXml\Validation\DtdValidator;
use DateTime;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ShipNoticeRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = DtdValidator::forDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.050/');
    }

    public function testMinimumExample(): void
    {
        $from = new Credential(
            'NetworkId',
            'AN00000123',
        );
        $to = new Credential(
            'NetworkId',
            'AN00000456',
        );
        $sender = new Credential(
            'NetworkId',
            'AN00000123',
            'abracadabra',
        );

        $statusUpdateRequest = ShipNoticeRequest::create(
            ShipNoticeHeader::create(
                'S2-123',
                new DateTime('2000-10-14T18:39:09-08:00'),
                new DateTime('2000-10-14T08:30:19-08:00'),
                new DateTime('2000-10-18T09:00:00-08:00'),
            )
                ->addCommentAsString('Got it all into one shipment.', null, 'en-CA'),
        )
            ->addShipControl(
                ShipControl::create(new CarrierIdentifier(CarrierIdentifier::DOMAIN_SCAC, 'FDE'), new ShipmentIdentifier('8202 8261 1194'))
                    ->addCarrierIdentifier('companyName', 'Federal Express'),
            )
            ->addShipNoticePortion(
                new ShipNoticePortion('32232995@hub.acme.com', 'DO1234'),
            );

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($statusUpdateRequest)
            ->build();

        $this->assertSame('ShipNoticeRequest_0c30050@supplierorg.com', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/ShipNoticeRequest.xml', $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '0c30050@supplierorg.com',
            new DateTime('2021-01-08T23:00:06-08:00'),
        );
    }
}
