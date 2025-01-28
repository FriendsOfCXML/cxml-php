<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Contact;
use CXml\Model\Credential;
use CXml\Model\Inventory;
use CXml\Model\InventoryQuantity;
use CXml\Model\ItemId;
use CXml\Model\Message\ProductActivityDetail;
use CXml\Model\Message\ProductActivityMessage;
use CXml\Model\MultilanguageString;
use CXml\Model\PayloadIdentity;
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
final class ProductActivityMessageTest extends TestCase implements PayloadIdentityFactoryInterface
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

        $productActivityMessage = ProductActivityMessage::create(
            'CP12465192-1552965424130',
            'SMI',
            new DateTime('2019-02-20T14:39:48-08:00'),
        )->addProductActivityDetail(
            ProductActivityDetail::create(
                new ItemId('SII99825', null, 'II99825'),
                Inventory::create()->setStockOnHandQuantity(new InventoryQuantity(200, 'EA')),
                Contact::create(new MultilanguageString('Warehouse', null, 'en'), 'locationFrom')
                    ->addIdReference('NetworkId', '0003'),
            ),
        );

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($productActivityMessage)
            ->build();

        $this->assertSame('ProductActivityMessage_0c30050@supplierorg.com', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/ProductActivityMessage.xml', $xml);

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
