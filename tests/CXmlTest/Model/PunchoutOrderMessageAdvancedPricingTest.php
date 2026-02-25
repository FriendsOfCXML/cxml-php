<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Classification;
use CXml\Model\Credential;
use CXml\Model\Description;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemIn;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PayloadIdentity;
use CXml\Model\PriceBasisQuantity;
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
final class PunchoutOrderMessageAdvancedPricingTest extends TestCase implements PayloadIdentityFactoryInterface
{
    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.050/');
    }

    public function testMinimumExampleAdvPricing(): void
    {
        $from = new Credential(
            'DUNS',
            '83528721',
        );
        $to = new Credential(
            'DUNS',
            '65652314',
        );
        $sender = new Credential(
            'workchairs.com',
            'website 1',
        );

        $punchoutOrderMessage = PunchOutOrderMessage::create(
            '1CX3L4843PPZO',
            new PunchOutOrderMessageHeader(new MoneyWrapper('USD', 10499)),
        )->addPunchoutOrderMessageItem(
            ItemIn::create(
                2,
                new ItemId('5555', null, 'KD5555'),
                ItemDetail::create(
                    Description::createWithShortName('Excelsior Desk Chair', null, 'en'),
                    'EA',
                    new MoneyWrapper('USD', 10499),
                    [
                        new Classification('UNSPSC', 'ean1234'),
                    ],
                    new PriceBasisQuantity(2, 0.5, 'BOX', new MultilanguageString('1 Box is 2 EA and the unit price is for 2', null, 'en')),
                ),
            ),
        );

        $cxml = Builder::create('Workchairs cXML Application', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($punchoutOrderMessage)
            ->build();

        $this->assertSame('PunchOutOrderMessage_933695160894', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);
        $this->dtdValidator->validateAgainstDtd($xml);

        $this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/PunchoutOrderMessageAdvancedPricing.xml', $xml);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '933695160894',
            new DateTime('2021-01-08T23:00:06-08:00'),
        );
    }
}
