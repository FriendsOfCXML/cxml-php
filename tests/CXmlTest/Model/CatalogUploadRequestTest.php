<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Credential;
use CXml\Model\Description;
use CXml\Model\MultilanguageString;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\CatalogUploadRequest;
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
final class CatalogUploadRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.050/');
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

        $statusUpdateRequest = new CatalogUploadRequest(
            'new',
            new MultilanguageString('Winter Prices', null, 'en'),
            new Description('premiere-level prices', null, 'en'),
            'https://www.attachment.com',
        );

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($statusUpdateRequest)
            ->build();

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../../metadata/cxml/samples/CatalogUploadRequest.xml', $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '0c30050@someone.com',
            new DateTime('2021-01-08T23:00:06-08:00'),
        );
    }
}
