<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Comment;
use CXml\Model\Credential;
use CXml\Model\DocumentReference;
use CXml\Model\InvoiceDetailHeaderIndicator;
use CXml\Model\InvoiceDetailHeaderOrder;
use CXml\Model\InvoiceDetailOrderInfo;
use CXml\Model\InvoiceDetailOrderSummary;
use CXml\Model\InvoiceDetailSummary;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\OrderReference;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\InvoiceDetailRequest;
use CXml\Model\Request\InvoiceDetailRequestHeader;
use CXml\Model\Tax;
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
final class InvoiceDetailRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
    private const SAMPLE = __DIR__ . '/../../metadata/cxml/samples/InvoiceDetailRequest.xml';

    private const SAMPLE_WITH_COMMENTS = __DIR__ . '/../../metadata/cxml/samples/InvoiceDetailRequestWithComments.xml';

    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.063/');
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

        $invoiceDetailRequest = InvoiceDetailRequest::create(
            InvoiceDetailRequestHeader::create(
                'INV-123',
                new DateTime('2021-01-08T10:00:00-08:00'),
                new InvoiceDetailHeaderIndicator(true),
            ),
            new InvoiceDetailSummary(
                new MoneyWrapper('EUR', 10000),
                new Tax('EUR', 1900, new MultilanguageString('19% VAT')),
                new MoneyWrapper('EUR', 11900),
            ),
        )
            ->addInvoiceDetailHeaderOrder(
                new InvoiceDetailHeaderOrder(
                    new InvoiceDetailOrderInfo(
                        new OrderReference(new DocumentReference('order-payload@buyerorg.com'), 'PO-456'),
                    ),
                    new InvoiceDetailOrderSummary(1, new MoneyWrapper('EUR', 10000)),
                ),
            );

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($invoiceDetailRequest)
            ->build();

        $this->assertSame('InvoiceDetailRequest_0c30050@supplierorg.com', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE, $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function testDeserialize(): void
    {
        $xml = (string)file_get_contents(self::SAMPLE);

        $cxml = Serializer::create()->deserialize($xml);

        $invoiceDetailRequest = $cxml->request?->payload;
        $this->assertInstanceOf(InvoiceDetailRequest::class, $invoiceDetailRequest);

        $header = $invoiceDetailRequest->invoiceDetailRequestHeader;
        $this->assertSame('INV-123', $header->invoiceId);
        $this->assertSame('2021-01-08T10:00:00-08:00', $header->invoiceDate->format(DATE_ATOM));
        $this->assertSame('yes', $header->invoiceDetailHeaderIndicator->isHeaderInvoice);

        $orders = $invoiceDetailRequest->getInvoiceDetailHeaderOrders();
        $this->assertCount(1, $orders);
        $this->assertSame('PO-456', $orders[0]->invoiceDetailOrderInfo->orderReference->orderId);
        $this->assertSame('order-payload@buyerorg.com', $orders[0]->invoiceDetailOrderInfo->orderReference->documentReference->payloadId);
        $this->assertSame(1, $orders[0]->invoiceDetailOrderSummary->invoiceLineNumber);
        $this->assertSame(10000, $orders[0]->invoiceDetailOrderSummary->subtotalAmount->money->getValueCent());

        $summary = $invoiceDetailRequest->invoiceDetailSummary;
        $this->assertSame(10000, $summary->subtotalAmount->money->getValueCent());
        $this->assertSame(1900, $summary->tax->money->getValueCent());
        $this->assertSame('19% VAT', $summary->tax->description->value);
        $this->assertSame(11900, $summary->netAmount->money->getValueCent());
        $this->assertSame('EUR', $summary->netAmount->money->currency);

        // serializing it again must result in the same (valid) document
        $reserialized = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE, $reserialized);
        $this->dtdValidator->validateAgainstDtd($reserialized);
    }

    public function testWithComments(): void
    {
        $invoiceDetailRequest = InvoiceDetailRequest::create(
            InvoiceDetailRequestHeader::create(
                'INV-123',
                new DateTime('2021-01-08T10:00:00-08:00'),
                new InvoiceDetailHeaderIndicator(true),
                new Comment('Please pay within 30 days.', 'note', 'en'),
            ),
            new InvoiceDetailSummary(
                new MoneyWrapper('EUR', 10000),
                new Tax('EUR', 1900, new MultilanguageString('19% VAT')),
                new MoneyWrapper('EUR', 11900),
            ),
        )
            ->addInvoiceDetailHeaderOrder(
                new InvoiceDetailHeaderOrder(
                    new InvoiceDetailOrderInfo(
                        new OrderReference(new DocumentReference('order-payload@buyerorg.com'), 'PO-456'),
                    ),
                    new InvoiceDetailOrderSummary(1, new MoneyWrapper('EUR', 10000), new Comment('Delivered in two shipments.', lang: 'en')),
                ),
            );

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from(new Credential('NetworkId', 'AN00000123'))
            ->to(new Credential('NetworkId', 'AN00000456'))
            ->sender(new Credential('NetworkId', 'AN00000123', 'abracadabra'))
            ->payload($invoiceDetailRequest)
            ->build();

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE_WITH_COMMENTS, $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function testDeserializeWithComments(): void
    {
        $xml = (string)file_get_contents(self::SAMPLE_WITH_COMMENTS);

        $cxml = Serializer::create()->deserialize($xml);

        $invoiceDetailRequest = $cxml->request?->payload;
        $this->assertInstanceOf(InvoiceDetailRequest::class, $invoiceDetailRequest);

        $headerComments = $invoiceDetailRequest->invoiceDetailRequestHeader->comments;
        $this->assertSame('Please pay within 30 days.', $headerComments?->value);
        $this->assertSame('note', $headerComments->type);
        $this->assertSame('en', $headerComments->lang);

        $orderComments = $invoiceDetailRequest->getInvoiceDetailHeaderOrders()[0]->invoiceDetailOrderSummary->comments;
        $this->assertSame('Delivered in two shipments.', $orderComments?->value);
        $this->assertNull($orderComments->type);

        $reserialized = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE_WITH_COMMENTS, $reserialized);
        $this->dtdValidator->validateAgainstDtd($reserialized);
    }

    public function testHeaderIndicatorOmitsAttributeByDefault(): void
    {
        $this->assertNull((new InvoiceDetailHeaderIndicator())->isHeaderInvoice);
        $this->assertSame('yes', (new InvoiceDetailHeaderIndicator(true))->isHeaderInvoice);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '0c30050@supplierorg.com',
            new DateTime('2021-01-08T23:00:06-08:00'),
        );
    }
}
