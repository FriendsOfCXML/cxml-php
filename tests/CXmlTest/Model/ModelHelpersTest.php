<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\CarrierIdentifier;
use CXml\Model\Classification;
use CXml\Model\Comment;
use CXml\Model\ConfirmationStatus;
use CXml\Model\Country;
use CXml\Model\CountryCode;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Description;
use CXml\Model\Header;
use CXml\Model\Index;
use CXml\Model\IndexItemInterface;
use CXml\Model\IndexItemWrapper;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemIn;
use CXml\Model\Message\Message;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\Message\QuoteMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\OrganizationId;
use CXml\Model\Party;
use CXml\Model\PayloadIdentity;
use CXml\Model\Phone;
use CXml\Model\PostalAddress;
use CXml\Model\QuoteItemIn;
use CXml\Model\Request\CatalogUploadRequest;
use CXml\Model\Request\ConfirmationHeader;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Model\Request\ProfileRequest;
use CXml\Model\Request\Request;
use CXml\Model\Request\ShipNoticeHeader;
use CXml\Model\Request\ShipNoticeRequest;
use CXml\Model\Response\Response;
use CXml\Model\ShipControl;
use CXml\Model\ShipmentIdentifier;
use CXml\Model\Status;
use CXml\Model\SupplierId;
use CXml\Model\TelephoneNumber;
use CXml\Model\TransportInformation;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ModelHelpersTest extends TestCase
{
    public function testCommentsTrait(): void
    {
        $header = ShipNoticeHeader::create('shipment-1');
        $this->assertNull($header->getComments());
        $this->assertNull($header->getCommentsAsString());

        $header
            ->addCommentAsString('first', 'type', 'en')
            ->addComment(new Comment(null, null, null, 'https://example.com/attachment.pdf'))
            ->addComment(new Comment(''))
            ->addCommentAsString('second');

        $this->assertCount(4, $header->getComments() ?? []);
        $this->assertSame("first\nsecond", $header->getCommentsAsString(), 'empty comments and attachments are skipped');
        $this->assertSame("first\nsecond", ShipNoticeRequest::create($header)->getCommentsAsString());
    }

    public function testExtrinsicsTrait(): void
    {
        $orderRequestHeader = $this->createOrderRequestHeader();
        $this->assertSame([], $orderRequestHeader->getExtrinsics());
        $this->assertNull($orderRequestHeader->getExtrinsicByName('foo'));

        $orderRequestHeader
            ->addExtrinsicAsKeyValue(' foo ', ' bar ')
            ->addExtrinsicAsKeyValue('foo', 'baz')
            ->addExtrinsicAsKeyValue('other', 'value');

        $this->assertCount(3, $orderRequestHeader->getExtrinsics());
        $this->assertSame('baz', $orderRequestHeader->getExtrinsicByName('foo')?->value, 'names are compared exactly');
        $this->assertSame(['foo' => 'baz', 'other' => 'value'], $orderRequestHeader->getExtrinsicsAsKeyValue(), 'keys and values are trimmed, last one wins');
    }

    public function testIdReferencesTrait(): void
    {
        $orderRequestHeader = $this->createOrderRequestHeader();
        $this->assertSame([], $orderRequestHeader->getIdReferences());
        $this->assertNull($orderRequestHeader->getIdReference('domain'));

        $orderRequestHeader->addIdReference('domain', 'first')->addIdReference('domain', 'second');

        $this->assertCount(2, $orderRequestHeader->getIdReferences());
        $this->assertSame('first', $orderRequestHeader->getIdReference('domain'));
        $this->assertNull($orderRequestHeader->getIdReference('other'));
    }

    #[DataProvider('provideEmptyPostalAddresses')]
    public function testPostalAddressIsEmpty(PostalAddress $postalAddress, bool $expectedEmpty): void
    {
        $this->assertSame($expectedEmpty, $postalAddress->isEmpty());
    }

    public static function provideEmptyPostalAddresses(): iterable
    {
        yield 'all empty' => [new PostalAddress([], [], '', new Country('DE')), true];
        yield 'empty strings' => [new PostalAddress([''], ['', '0'], '0', new Country('DE'), '', '', '', ''), true];
        yield 'with city' => [new PostalAddress([], [], 'Berlin', new Country('DE')), false];
        yield 'with street' => [new PostalAddress([], ['Main St'], '', new Country('DE')), false];
        yield 'with deliverTo' => [new PostalAddress(['John'], [], '', new Country('DE')), false];
        yield 'with postal code' => [new PostalAddress([], [], '', new Country('DE'), null, null, '10115'), false];
        yield 'with name' => [new PostalAddress([], [], '', new Country('DE'), null, null, null, 'home'), false];
    }

    public function testPhoneAsString(): void
    {
        $this->assertSame('+49 (30) 1234567', (new Phone(new TelephoneNumber(new CountryCode('DE', '49'), '30', '1234567')))->getTelephoneNumberAsString());
        $this->assertSame('+1 (800) 5551212 -42', (new Phone(new TelephoneNumber(new CountryCode('US', '1'), '800', '5551212', '42')))->getTelephoneNumberAsString());
        $this->assertSame('+ (800) 5551212', (new Phone(new TelephoneNumber(new CountryCode('US'), '800', '5551212')))->getTelephoneNumberAsString());
        $this->assertNull((new Phone(new TelephoneNumber(new CountryCode('US', '1'), '800', '')))->getTelephoneNumberAsString());
    }

    public function testShipControl(): void
    {
        $shipControl = ShipControl::create(
            new CarrierIdentifier(CarrierIdentifier::DOMAIN_SCAC, 'UPS'),
            new ShipmentIdentifier('1Z999', 'trackingNumber'),
        )->addCarrierIdentifier(CarrierIdentifier::DOMAIN_COMPANYNAME, 'United Parcel Service');

        $this->assertSame('UPS', $shipControl->getCarrierIdentifier(CarrierIdentifier::DOMAIN_SCAC));
        $this->assertSame('United Parcel Service', $shipControl->getCarrierIdentifier(CarrierIdentifier::DOMAIN_COMPANYNAME));
        $this->assertNull($shipControl->getCarrierIdentifier(CarrierIdentifier::DOMAIN_IATA));
        $this->assertSame('1Z999', $shipControl->getShipmentIdentifier('trackingNumber'));
        $this->assertNull($shipControl->getShipmentIdentifier(), 'only identifiers without domain match null');
        $this->assertCount(2, $shipControl->getCarrierIdentifiers());
        $this->assertCount(1, $shipControl->getShipmentIdentifiers());
    }

    public function testShipNoticeHeader(): void
    {
        $header = ShipNoticeHeader::create('shipment-1', null, null, null, '');
        $this->assertNull($header->documentReference);
        $this->assertInstanceOf(DateTime::class, $header->noticeDate, 'notice date defaults to now');

        $this->assertSame('order-payload-id', ShipNoticeHeader::create('shipment-1', null, null, null, 'order-payload-id')->documentReference?->payloadId);
    }

    public function testTransportInformation(): void
    {
        $transportInformation = TransportInformation::create('account-1', 'express');
        $this->assertSame('express', $transportInformation->route?->value);
        $this->assertSame('account-1', $transportInformation->shippingContractNumber?->value);

        $empty = TransportInformation::create();
        $this->assertNull($empty->route);
        $this->assertNull($empty->shippingContractNumber);
    }

    public function testDescription(): void
    {
        $description = Description::createWithShortName('Short', 'text', 'de');

        $this->assertNull($description->value);
        $this->assertSame('text', $description->type);
        $this->assertSame('de', $description->lang);
    }

    public function testCXmlToString(): void
    {
        $identity = new PayloadIdentity('payload-1');

        $this->assertSame('ProfileRequest_payload-1', (string)CXml::forRequest($identity, new Request(new ProfileRequest()), $this->createHeader()));
        $this->assertSame('Response_payload-1', (string)CXml::forResponse($identity, new Response(new Status())));
    }

    #[DataProvider('provideInvalidModels')]
    public function testInvalidModelIsRejected(callable $factory): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory();
    }

    public static function provideInvalidModels(): iterable
    {
        yield 'request deploymentMode' => [static fn (): object => new Request(new ProfileRequest(), null, 'staging')];
        yield 'message deploymentMode' => [static fn (): object => new Message(PunchOutOrderMessage::create('c', new PunchOutOrderMessageHeader(new MoneyWrapper('EUR', 1))), null, null, null, 'staging')];
        yield 'order request type' => [static fn (): object => OrderRequestHeader::create('1', new DateTime(), null, new BillTo(new Address(new MultilanguageString('x'))), new MoneyWrapper('EUR', 1), 'cancel')];
        yield 'confirmation header type' => [static fn (): object => ConfirmationHeader::create(ConfirmationHeader::TYPE_UNKNOWN)];
        yield 'confirmation status type' => [static fn (): object => new ConfirmationStatus(1, 'EA', self::createItemIn(), null, null, ConfirmationHeader::TYPE_EXCEPT)];
        yield 'quote message type' => [static fn (): object => new QuoteMessageHeader(new OrganizationId(new Credential('d', 'i')), new MoneyWrapper('EUR', 1), 'unknown', 'q-1', new DateTime(), 'EUR')];
        yield 'quote item type' => [static fn (): object => QuoteItemIn::create('unknown', 1, new ItemId('sku'), self::createItemDetail())];
        yield 'catalog upload operation' => [static fn (): object => new CatalogUploadRequest('delete', new MultilanguageString('name'), new Description('desc'), 'https://example.com/catalog.cif')];
        yield 'index loadmode' => [static fn (): object => Index::create('Partial', new SupplierId('DUNS', '123'))];
        yield 'item detail without classification' => [static fn (): object => ItemDetail::create(new Description('desc'), 'EA', new MoneyWrapper('EUR', 1), [])];
    }

    public function testIndexItemWrapperRejectsUnknownItems(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid index item type');

        (new IndexItemWrapper())->addIndexItem(new class implements IndexItemInterface {});
    }

    private static function createItemDetail(): ItemDetail
    {
        return ItemDetail::create(new Description('desc'), 'EA', new MoneyWrapper('EUR', 1), [new Classification('custom', '0')]);
    }

    private static function createItemIn(): ItemIn
    {
        return ItemIn::create(1, new ItemId('sku'), self::createItemDetail());
    }

    private function createOrderRequestHeader(): OrderRequestHeader
    {
        return OrderRequestHeader::create('1', new DateTime(), null, new BillTo(new Address(new MultilanguageString('x'))), new MoneyWrapper('EUR', 1));
    }

    private function createHeader(): Header
    {
        $credential = new Credential('NetworkId', 'id');

        return new Header(new Party($credential), new Party($credential), new Party($credential));
    }
}
