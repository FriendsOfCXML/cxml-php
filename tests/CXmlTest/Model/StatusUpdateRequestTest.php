<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Comment;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\DocumentInfo;
use CXml\Model\DocumentStatus;
use CXml\Model\InvoiceIDInfo;
use CXml\Model\InvoiceStatus;
use CXml\Model\MoneyWrapper;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\StatusUpdateRequest;
use CXml\Model\Status;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Serializer;
use CXml\Validation\DtdValidator;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class StatusUpdateRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
    private const SAMPLE_WITH_DOCUMENT_STATUS = __DIR__ . '/../../metadata/cxml/samples/StatusUpdateRequestWithDocumentStatus.xml';

    private const SAMPLE_WITH_INVOICE_STATUS = __DIR__ . '/../../metadata/cxml/samples/StatusUpdateRequestWithInvoiceStatus.xml';

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

        $statusUpdateRequest = new StatusUpdateRequest(
            new Status(200, 'OK', 'Forwarded to supplier', 'en-US'),
            '0c300508b7863dcclb_14999',
        );

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($statusUpdateRequest)
            ->build();

        $this->assertSame('StatusUpdateRequest_0c30050@supplierorg.com', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/StatusUpdateRequest.xml', $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function testWithExtrinsicsKeepsDtdOrder(): void
    {
        $statusUpdateRequest = StatusUpdateRequest::create(
            new Status(200, 'OK', 'Forwarded to supplier', 'en-US'),
            '0c300508b7863dcclb_14999',
        );
        $statusUpdateRequest->addExtrinsicAsKeyValue('trackingId', '4711');

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from(new Credential('NetworkId', 'AN00000123'))
            ->to(new Credential('NetworkId', 'AN00000456'))
            ->sender(new Credential('NetworkId', 'AN00000123', 'abracadabra'))
            ->payload($statusUpdateRequest)
            ->build();

        $xml = Serializer::create()->serialize($cxml);

        // DTD: (DocumentReference?, Status, (PaymentStatus | ...)?, Extrinsic*)
        $this->dtdValidator->validateAgainstDtd($xml);
        $this->assertMatchesRegularExpression('#<DocumentReference [^>]+/>\s*<Status [^>]+>[^<]*</Status>\s*<Extrinsic name="trackingId">4711</Extrinsic>#', $xml);
    }

    public function testEmptyDocumentReferenceIsOmitted(): void
    {
        $this->assertNull(StatusUpdateRequest::create(new Status())->documentReference);
        $this->assertNull(StatusUpdateRequest::create(new Status(), '')->documentReference);
        $this->assertSame('ref', StatusUpdateRequest::create(new Status(), 'ref')->documentReference?->payloadId);
    }

    public function testWithDocumentStatusAndComments(): void
    {
        $documentStatus = DocumentStatus::create(
            'accepted',
            new DocumentInfo('PO-456', 'OrderRequest', new DateTime('2021-01-08T10:00:00-08:00')),
        )
            ->addComment(new Comment('Order accepted, shipping next week.', 'note', 'en'))
            ->addCommentAsString('Bestellung angenommen.', lang: 'de');

        $statusUpdateRequest = StatusUpdateRequest::create(
            new Status(200, 'OK', 'Forwarded to supplier', 'en-US'),
            '0c300508b7863dcclb_14999',
            documentStatus: $documentStatus,
        );

        $xml = Serializer::create()->serialize($this->buildCXml($statusUpdateRequest));
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE_WITH_DOCUMENT_STATUS, $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function testDeserializeWithDocumentStatus(): void
    {
        $xml = (string)file_get_contents(self::SAMPLE_WITH_DOCUMENT_STATUS);

        $cxml = Serializer::create()->deserialize($xml);

        $statusUpdateRequest = $cxml->request?->payload;
        $this->assertInstanceOf(StatusUpdateRequest::class, $statusUpdateRequest);
        $this->assertNull($statusUpdateRequest->invoiceStatus);

        $documentStatus = $statusUpdateRequest->documentStatus;
        $this->assertInstanceOf(DocumentStatus::class, $documentStatus);
        $this->assertSame('accepted', $documentStatus->type);
        $this->assertSame('PO-456', $documentStatus->documentInfo?->documentId);
        $this->assertSame('OrderRequest', $documentStatus->documentInfo->documentType);

        $comments = $documentStatus->getComments();
        $this->assertCount(2, $comments ?? []);
        $this->assertSame('Order accepted, shipping next week.', $comments[0]->value);
        $this->assertSame('note', $comments[0]->type);
        $this->assertSame('en', $comments[0]->lang);
        $this->assertSame("Order accepted, shipping next week.\nBestellung angenommen.", $documentStatus->getCommentsAsString());

        $reserialized = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE_WITH_DOCUMENT_STATUS, $reserialized);
        $this->dtdValidator->validateAgainstDtd($reserialized);
    }

    public function testWithInvoiceStatusAndComments(): void
    {
        $invoiceStatus = InvoiceStatus::create(
            InvoiceStatus::TYPE_PAID,
            new InvoiceIDInfo('INV-123', new DateTime('2021-01-08T10:00:00-08:00')),
            new MoneyWrapper('EUR', 5000),
            new DateTime('2021-02-08T00:00:00-08:00'),
        )->addCommentAsString('Partial payment, remainder follows.', lang: 'en');

        $statusUpdateRequest = StatusUpdateRequest::create(
            new Status(200, 'OK', 'Forwarded to supplier', 'en-US'),
            '0c300508b7863dcclb_14999',
            invoiceStatus: $invoiceStatus,
        );
        $statusUpdateRequest->addExtrinsicAsKeyValue('trackingId', '4711');

        $xml = Serializer::create()->serialize($this->buildCXml($statusUpdateRequest));

        // DTD: InvoiceStatus must come after Status and before Extrinsic
        $this->dtdValidator->validateAgainstDtd($xml);
        $this->assertMatchesRegularExpression('#</Status>\s*<InvoiceStatus [^>]+>.*</InvoiceStatus>\s*<Extrinsic name="trackingId">4711</Extrinsic>#s', $xml);
    }

    public function testDeserializeWithInvoiceStatus(): void
    {
        $xml = (string)file_get_contents(self::SAMPLE_WITH_INVOICE_STATUS);

        $cxml = Serializer::create()->deserialize($xml);

        $statusUpdateRequest = $cxml->request?->payload;
        $this->assertInstanceOf(StatusUpdateRequest::class, $statusUpdateRequest);
        $this->assertNull($statusUpdateRequest->documentStatus);

        $invoiceStatus = $statusUpdateRequest->invoiceStatus;
        $this->assertInstanceOf(InvoiceStatus::class, $invoiceStatus);
        $this->assertSame(InvoiceStatus::TYPE_PAID, $invoiceStatus->type);
        $this->assertSame('INV-123', $invoiceStatus->invoiceIdInfo?->invoiceId);
        $this->assertSame(5000, $invoiceStatus->partialAmount?->money->getValueCent());
        $this->assertSame('Partial payment, remainder follows.', $invoiceStatus->getCommentsAsString());

        $reserialized = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile(self::SAMPLE_WITH_INVOICE_STATUS, $reserialized);
        $this->dtdValidator->validateAgainstDtd($reserialized);
    }

    public function testInvalidInvoiceStatusTypeIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        InvoiceStatus::create('approved');
    }

    public function testDocumentStatusAndInvoiceStatusAreMutuallyExclusive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        StatusUpdateRequest::create(
            new Status(),
            documentStatus: DocumentStatus::create('accepted'),
            invoiceStatus: InvoiceStatus::create(InvoiceStatus::TYPE_PAID),
        );
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '0c30050@supplierorg.com',
            new DateTime('2021-01-08T23:00:06-08:00'),
        );
    }

    private function buildCXml(StatusUpdateRequest $statusUpdateRequest): CXml
    {
        return Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from(new Credential('NetworkId', 'AN00000123'))
            ->to(new Credential('NetworkId', 'AN00000456'))
            ->sender(new Credential('NetworkId', 'AN00000123', 'abracadabra'))
            ->payload($statusUpdateRequest)
            ->build();
    }
}
