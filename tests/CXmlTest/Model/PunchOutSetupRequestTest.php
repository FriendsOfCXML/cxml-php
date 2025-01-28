<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Address;
use CXml\Model\Classification;
use CXml\Model\Country;
use CXml\Model\CountryCode;
use CXml\Model\Credential;
use CXml\Model\Description;
use CXml\Model\Extrinsic;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemOut;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PayloadIdentity;
use CXml\Model\Phone;
use CXml\Model\PostalAddress;
use CXml\Model\Request\PunchOutSetupRequest;
use CXml\Model\SelectedItem;
use CXml\Model\ShipTo;
use CXml\Model\TelephoneNumber;
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
final class PunchOutSetupRequestTest extends TestCase implements PayloadIdentityFactoryInterface
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
            'inbound@prominate-platform.com',
        );
        $to = new Credential(
            'NetworkId',
            'supplier@supplier.com',
        );
        $sender = new Credential(
            'NetworkId',
            'inbound@prominate-platform.com',
            's3cr3t',
        );

        $punchoutSetupRequest = (new PunchOutSetupRequest(
            '550bce3e592023b2e7b015307f965133',
            'https://prominate-platform.com/hook-url',
            'http://workchairs.com/cxml',
            new ShipTo(
                new Address(
                    new MultilanguageString('Acme'),
                    new PostalAddress(
                        [
                            'Joe Smith',
                            'Mailstop M-543',
                        ],
                        [
                            '123 Anystreet',
                        ],
                        'Sunnyvale',
                        new Country('US', 'United States'),
                        null,
                        'CA',
                        '90489',
                        'default',
                    ),
                    null,
                    null,
                    null,
                    new Phone(
                        new TelephoneNumber(
                            new CountryCode('US', '1'),
                            '800',
                            '5551212',
                        ),
                        'personal',
                    ),
                ),
            ),
            new SelectedItem(
                new ItemId('4545321', null, 'II99825'),
            ),
        ))->addItem(
            ItemOut::create(
                10,
                2,
                new ItemId('5555'),
                ItemDetail::create(
                    Description::createWithShortName('Excelsior Desk Chair', null, 'en'),
                    'EA',
                    new MoneyWrapper('EUR', 76320),
                    [
                        new Classification('UNSPSC', 'ean1234'),
                    ],
                ),
                new DateTime('2023-01-23T16:00:06-01:00'),
            ),
        )->addItem(
            ItemOut::create(
                20,
                1,
                new ItemId('6666'),
                ItemDetail::create(
                    Description::createWithShortName('22Excelsior Desk Chair', null, 'en'),
                    'EA',
                    new MoneyWrapper('EUR', 76420),
                    [
                        new Classification('UNSPSC', 'ean1230'),
                    ],
                ),
                new DateTime('2023-01-23T16:00:06-01:00'),
            ),
        );

        $punchoutSetupRequest->addExtrinsic(
            new Extrinsic('UserEmail', 'john-doe@domain.com'),
        );

        $cxml = Builder::create('Workchairs cXML Application', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($punchoutSetupRequest)
            ->build('test');

        $this->assertSame('PunchOutSetupRequest_933695160890', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);

        $this->dtdValidator->validateAgainstDtd($xml);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../../metadata/cxml/samples/PunchOutSetupRequest.xml', $xml);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '933695160890',
            new DateTime('2023-01-23T16:00:06-01:00'),
        );
    }
}
