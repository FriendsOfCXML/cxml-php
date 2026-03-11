<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Model\AttachmentReference;
use CXml\Model\Characteristic;
use CXml\Model\Classification;
use CXml\Model\Description;
use CXml\Model\Index;
use CXml\Model\IndexItemAdd;
use CXml\Model\IndexItemDetail;
use CXml\Model\ItemDetail;
use CXml\Model\ItemDetailIndustry;
use CXml\Model\ItemId;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PriceBasisQuantity;
use CXml\Model\SupplierId;
use CXml\Serializer;
use CXml\Validation\DtdValidator;
use DateTime;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class IndexTest extends TestCase
{
    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.050/');
    }

    public function testMinimumExample(): void
    {
        $itemDetailIndustry = new ItemDetailIndustry([
            new Characteristic('size', 'L'),
            new Characteristic('color', 'red'),
        ]);
        $attachmentReference = new AttachmentReference(
            new MultilanguageString('Main Image', null, 'en'),
            new MultilanguageString('Main Image of product', null, 'en'),
            'main_image',
            'https://example.com/product/image.jpg',
        );

        $index = Index::create(
            'Full',
            new SupplierId('supplier', 'id'),
        )
            ->addIndexItem(
                IndexItemAdd::create(
                    new ItemId('PROMINATE-SKU'),
                    ItemDetail::create(
                        new Description('English description', null, 'en'),
                        'EA',
                        new MoneyWrapper('EUR', 123),
                        [
                            new Classification('ProminateCategoryCode', 'Merchandise', 'partner_merchandise'),
                        ],
                        new PriceBasisQuantity(
                            10,
                            0.1,
                            'BX',
                        ),
                    )
                        ->setUrl('https://example.com/product')
                        ->setLeadtime(2)
                        ->addDescription(new Description('German description', null, 'de'))
                        ->addExtrinsicAsKeyValue('on_demand', 'true')
                        ->addExtrinsicAsKeyValue('custom', 'custom_note')
                        ->setItemDetailIndustry($itemDetailIndustry)
                        ->addAttachmentReference($attachmentReference),
                    new IndexItemDetail(
                        2,
                        new DateTime('2026-02-05T09:46:33+00:00'),
                        [
                            'DE', 'CH', 'AT',
                        ],
                    ),
                ),
            )
            ->addCommentAsString('comment');

        $xml = Serializer::create()->serialize($index);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../../metadata/cxml/samples/Index.xml', $xml);

        $this->dtdValidator->validateAgainstDtd($xml);
    }
}
