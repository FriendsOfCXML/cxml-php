<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Address;
use CXml\Model\Contact;
use CXml\Model\Country;
use CXml\Model\CountryCode;
use CXml\Model\Credential;
use CXml\Model\Message\QuoteMessage;
use CXml\Model\Message\QuoteMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\OrganizationId;
use CXml\Model\PayloadIdentity;
use CXml\Model\Phone;
use CXml\Model\PostalAddress;
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
final class QuoteMessageTest extends TestCase implements PayloadIdentityFactoryInterface
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

        $organizationId = new OrganizationId(
            new Credential(
                'domain',
                'identity',
            ),
        );

        $total = new MoneyWrapper('USD', 10000);

        $quoteMessage = QuoteMessage::create(
            $organizationId,
            $total,
            QuoteMessageHeader::TYPE_ACCEPT,
            'quoteId',
            new DateTime('2021-01-08T23:00:06-08:00'),
            'de',
        );

        $contact = Contact::create(new MultilanguageString('Joe Smith'))
            ->addEmail('joe.smith@siemens.com')
            ->addIdReference('GUID', '123456');

        $shipTo = new ShipTo(
            new Address(
                new MultilanguageString('Acme Inc.'),
                new PostalAddress(
                    ['Acme Inc.', 'Joe Smith'],
                    ['123 Anystreet'],
                    'Sunnyvale',
                    new Country('US', 'United States'),
                    null,
                    'CA',
                    '90489',
                ),
                null,
                null,
                null,
                new Phone(
                    new TelephoneNumber(
                        new CountryCode('US', '1'),
                        '800',
                        '1234567',
                    ),
                    'company',
                ),
            ),
        );

        $quoteMessage->getQuoteMessageHeader()
            ->addContact($contact)
            ->setShipTo($shipTo)
            ->addExtrinsicAsKeyValue('expiry_date', '2023-01-08T23:00:06-08:00')
            ->addCommentAsString('This is a comment');

        $cxml = Builder::create('Supplier’s Super Order Processor', 'en-US', $this)
            ->from($from)
            ->to($to)
            ->sender($sender)
            ->payload($quoteMessage)
            ->build();

        $this->assertSame('QuoteMessage_0c30050@supplierorg.com', (string)$cxml);

        $xml = Serializer::create()->serialize($cxml);
        $this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/QuoteMessage.xml', $xml);

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
