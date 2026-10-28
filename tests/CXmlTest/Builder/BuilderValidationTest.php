<?php

declare(strict_types=1);

namespace CXmlTest\Builder;

use CXml\Builder\OrderRequestBuilder;
use CXml\Builder\ProductActivityMessageBuilder;
use CXml\Builder\PunchOutOrderMessageBuilder;
use CXml\Model\Classification;
use CXml\Model\Contact;
use CXml\Model\Country;
use CXml\Model\Description;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemIn;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\PostalAddress;
use CXml\Model\PriceBasisQuantity;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Serializer;
use DateTime;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
#[CoversNothing]
final class BuilderValidationTest extends TestCase
{
    public function testOrderRequestBuilderRequiresBillTo(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('BillTo is required');

        OrderRequestBuilder::create('order-1', new DateTime(), 'EUR')->build();
    }

    public function testOrderRequestBuilderTotalAndLineNumbers(): void
    {
        $builder = OrderRequestBuilder::create('order-1', new DateTime('2025-01-01'), 'EUR', 'de')
            ->billTo('Acme');

        $parent = $builder->addItem(2, new ItemId('sku-1'), 'Item 1', 'EA', 1999, [new Classification('custom', '0')]);
        $child = $builder->addItem(3, new ItemId('sku-2'), 'Item 2', 'EA', 1, [new Classification('custom', '0')], null, $parent);
        // price is for 10 pieces
        $builder->addItem(5, new ItemId('sku-3'), 'Item 3', 'EA', 1000, [new Classification('custom', '0')], null, null, new PriceBasisQuantity(10, 1, 'EA'));

        $orderRequest = $builder
            ->shipping(500, 'Standard')
            ->tax(380, 'VAT', [['category' => 'vat', 'amount' => 380, 'rate' => 19, 'type' => 'standard']])
            ->addComment('please hurry')
            ->addContact('John', 'john@example.com')
            ->addExtrinsic('costCenter', '4711')
            ->build();

        $this->assertSame(1, $parent->lineNumber);
        $this->assertSame(2, $child->lineNumber);
        $this->assertSame(1, $child->parentLineNumber);
        $this->assertCount(3, $orderRequest->getItems());
        $this->assertSame($builder->getItems(), $orderRequest->getItems());

        $header = $orderRequest->orderRequestHeader;
        // 2 * 19.99 + 3 * 0.01 + 5 * (1 / 10) * 10.00 (shipping and tax are not part of the total)
        $this->assertSame(3998 + 3 + 500, $header->total->money->getValueCent());
        $this->assertSame('EUR', $header->total->money->currency);
        $this->assertSame(OrderRequestHeader::TYPE_NEW, $header->type);
        $this->assertSame('please hurry', $header->getCommentsAsString());
        $this->assertSame('john@example.com', $header->getContactByRole(Contact::ROLE_BUYER)?->getEmail());
        $this->assertSame('de', $header->getContactByRole(Contact::ROLE_BUYER)?->getName()->lang);
        $this->assertSame(['costCenter' => '4711'], $header->getExtrinsicsAsKeyValue());
        $this->assertSame(500, $header->getShipping()?->money->getValueCent());
        $this->assertSame(380, $header->getTax()?->money->getValueCent());
    }

    public function testOrderRequestBuilderTaxDetailsMustBeValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tax detail must contain "amount"');

        OrderRequestBuilder::create('order-1', new DateTime(), 'EUR')->tax(100, 'VAT', [['category' => 'vat']]);
    }

    public function testOrderRequestBuilderIdReferencesMustBeStrings(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderRequestBuilder::create('order-1', new DateTime(), 'EUR')->shipTo(
            'Acme',
            new PostalAddress([], [], 'City', new Country('DE')),
            [],
            null,
            null,
            [4711],
        );
    }

    public function testFromPunchOutOrderMessageRequiresOrderId(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('orderId should either be given');

        OrderRequestBuilder::fromPunchOutOrderMessage(PunchOutOrderMessageBuilder::create('en', 'cookie', 'EUR')->build());
    }

    public function testFromPunchOutOrderMessageRequiresOrderDate(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('orderDate should either be given');

        OrderRequestBuilder::fromPunchOutOrderMessage(PunchOutOrderMessageBuilder::create('en', 'cookie', 'EUR')->orderReference('order-1')->build());
    }

    public function testFromDeserializedPunchOutOrderMessageKeepsExactPrices(): void
    {
        $xml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML payloadID="x" timestamp="2022-06-07T10:09:56+00:00">
                <Message>
                    <PunchOutOrderMessage>
                        <BuyerCookie>cookie</BuyerCookie>
                        <PunchOutOrderMessageHeader operationAllowed="create">
                            <Total><Money currency="EUR">40.27</Money></Total>
                            <SupplierOrderInfo orderID="order-1" orderDate="2025-01-01T00:00:00+00:00"/>
                        </PunchOutOrderMessageHeader>
                        <ItemIn quantity="2">
                            <ItemID><SupplierPartID>sku-1</SupplierPartID></ItemID>
                            <ItemDetail>
                                <UnitPrice><Money currency="EUR">19.99</Money></UnitPrice>
                                <Description xml:lang="en">Item 1</Description>
                                <UnitOfMeasure>EA</UnitOfMeasure>
                                <Classification domain="UNSPSC">123</Classification>
                            </ItemDetail>
                        </ItemIn>
                        <ItemIn quantity="1">
                            <ItemID><SupplierPartID>sku-2</SupplierPartID></ItemID>
                            <ItemDetail>
                                <UnitPrice><Money currency="EUR">0.29</Money></UnitPrice>
                                <Description xml:lang="en">Item 2</Description>
                                <UnitOfMeasure>EA</UnitOfMeasure>
                                <Classification domain="UNSPSC">123</Classification>
                            </ItemDetail>
                        </ItemIn>
                    </PunchOutOrderMessage>
                </Message>
            </cXML>
            XML;

        /** @var PunchOutOrderMessage $punchOutOrderMessage */
        $punchOutOrderMessage = Serializer::create()->deserialize($xml)->message?->payload;

        $orderRequest = OrderRequestBuilder::fromPunchOutOrderMessage($punchOutOrderMessage)
            ->billTo('Acme')
            ->build();

        $items = $orderRequest->getItems();
        $this->assertSame('order-1', $orderRequest->orderRequestHeader->orderId);
        $this->assertSame('19.99', $items[0]->itemDetail->unitPrice->money->value);
        $this->assertSame('0.29', $items[1]->itemDetail->unitPrice->money->value);
        $this->assertSame('40.27', $orderRequest->orderRequestHeader->total->money->value);
    }

    public function testPunchOutOrderMessageBuilder(): void
    {
        $punchOutOrderMessage = PunchOutOrderMessageBuilder::create('en', 'cookie', 'EUR', PunchOutOrderMessageHeader::OPERATION_EDIT)
            ->orderReference('order-1', new DateTime('2025-01-01'))
            ->addPunchoutOrderMessageItem(new ItemId('sku-1'), 2, 'Item 1', 'EA', 1999, [new Classification('custom', '0')], 'mpn', 'maker', 3, ['color' => 'red'])
            ->addItem(ItemIn::create(
                10,
                new ItemId('sku-2'),
                ItemDetail::create(new Description('Item 2'), 'EA', new MoneyWrapper('EUR', 250), [new Classification('custom', '0')], new PriceBasisQuantity(100, 1, 'EA')),
            ))
            ->shipping(500, 'Standard')
            ->tax(100, 'VAT')
            ->build();

        $header = $punchOutOrderMessage->punchOutOrderMessageHeader;
        $this->assertSame('cookie', $punchOutOrderMessage->buyerCookie);
        $this->assertSame(PunchOutOrderMessageHeader::OPERATION_EDIT, $header->getOperationAllowed());
        $this->assertSame('order-1', $header->getSupplierOrderInfo()?->orderId);
        $this->assertNull($header->getShipTo());
        // 2 * 19.99 + 10 * (1 / 100) * 2.50
        $this->assertSame(3998 + 25, $header->total->money->getValueCent());
        $this->assertSame(500, $header->shipping?->money->getValueCent());
        $this->assertSame(100, $header->tax?->money->getValueCent());

        $items = $punchOutOrderMessage->getPunchoutOrderMessageItems();
        $this->assertCount(2, $items);
        $this->assertSame('mpn', $items[0]->itemDetail->getManufacturerPartId());
        $this->assertSame('maker', $items[0]->itemDetail->getManufacturerName());
        $this->assertSame(3, $items[0]->itemDetail->getLeadtime());
        $this->assertSame('red', $items[0]->itemDetail->getExtrinsicByName('color')?->value);
    }

    public function testPunchOutOrderMessageHeaderDefaultsToCreate(): void
    {
        $this->assertSame(PunchOutOrderMessageHeader::OPERATION_CREATE, PunchOutOrderMessageBuilder::create('en', 'cookie', 'EUR')->build()->punchOutOrderMessageHeader->getOperationAllowed());
    }

    public function testPunchOutOrderMessageHeaderRejectsInvalidOperation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PunchOutOrderMessageHeader(new MoneyWrapper('EUR', 1), null, null, 'delete');
    }

    public function testProductActivityMessageBuilder(): void
    {
        $message = ProductActivityMessageBuilder::create('msg-1', 'warehouseCode')
            ->addProductActivityDetail('sku-1', 'WH1', 10, ['batch' => 'b1'])
            ->addProductActivityDetail('sku-2', 'WH2', 0)
            ->build();

        $this->assertSame('msg-1', $message->getProductActivityHeader()->messageId);

        $details = $message->getProductActivityDetails();
        $this->assertCount(2, $details);
        $this->assertSame('sku-1', $details[0]->itemId->supplierPartId);
        $this->assertSame(10, $details[0]->inventory?->getStockOnHandQuantity()?->quantity);
        $this->assertSame('WH1', $details[0]->contact?->getIdReference('warehouseCode'));
        $this->assertSame(['batch' => 'b1'], $details[0]->getExtrinsicsAsKeyValue());
        $this->assertSame([], $details[1]->getExtrinsics());
    }

    public function testProductActivityMessageBuilderRequiresDetails(): void
    {
        $this->expectException(RuntimeException::class);

        ProductActivityMessageBuilder::create('msg-1', 'warehouseCode')->build();
    }
}
