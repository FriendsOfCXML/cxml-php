<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\Contact;
use CXml\Model\Credential;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Model\Request\PunchOutSetupRequest;
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
final class ContactTest extends TestCase implements PayloadIdentityFactoryInterface
{
    public function testAccessors(): void
    {
        $contact = Contact::create(new MultilanguageString('John Doe'), Contact::ROLE_BUYER)
            ->addEmail('john@example.com')
            ->addURL('https://example.com/john');

        $this->assertSame('John Doe', $contact->getName()->value);
        $this->assertSame(Contact::ROLE_BUYER, $contact->getRole());
        $this->assertSame('john@example.com', $contact->getEmail());
        $this->assertSame('https://example.com/john', $contact->getURL());
    }

    public function testDefaults(): void
    {
        $contact = Contact::create(new MultilanguageString('John Doe'));

        $this->assertNull($contact->getRole());
        $this->assertNull($contact->getEmail());
        $this->assertNull($contact->getURL());
        $this->assertSame([], $contact->getIdReferences());
        $this->assertSame([], $contact->getExtrinsics());
    }

    public function testFullContactInPunchOutSetupRequestIsDtdValid(): void
    {
        $contact = Contact::create(new MultilanguageString('John Doe', null, 'en'), Contact::ROLE_ENDUSER)
            ->addEmail('john@example.com')
            ->addURL('https://example.com/john')
            ->addIdReference('buyerId', '4711')
            ->addExtrinsicAsKeyValue('department', 'IT');

        $punchOutSetupRequest = new PunchOutSetupRequest(
            'buyer-cookie',
            'https://buyer.example.com/hook',
            'https://supplier.example.com/punchout',
            null,
            null,
            $contact,
        );

        $cxml = Builder::create('cxml-php UserAgent', 'en-US', $this)
            ->from(new Credential('NetworkId', 'buyer'))
            ->to(new Credential('NetworkId', 'supplier'))
            ->sender(new Credential('NetworkId', 'buyer', 's3cr3t'))
            ->payload($punchOutSetupRequest)
            ->build();

        $xml = Serializer::create()->serialize($cxml);

        // DTD defines the order: (Name, PostalAddress*, Email*, Phone*, Fax*, URL*, IdReference*, Extrinsic*)
        DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.063')->validateAgainstDtd($xml);

        $this->assertMatchesRegularExpression(
            '#<Contact role="endUser">\s*<Name xml:lang="en">John Doe</Name>\s*<Email>john@example.com</Email>\s*<URL>https://example.com/john</URL>\s*<IdReference domain="buyerId" identifier="4711"/>\s*<Extrinsic name="department">IT</Extrinsic>\s*</Contact>#',
            $xml,
        );

        // Contact must be placed between BrowserFormPost and SupplierSetup
        $this->assertMatchesRegularExpression('#</BrowserFormPost>\s*<Contact.+</Contact>\s*<SupplierSetup>#s', $xml);

        // and survive a roundtrip
        $deserialized = Serializer::create()->deserialize($xml);

        /** @var PunchOutSetupRequest $deserializedPayload */
        $deserializedPayload = $deserialized->request?->payload;
        $deserializedContact = $deserializedPayload->contact;

        $this->assertInstanceOf(Contact::class, $deserializedContact);
        $this->assertSame('https://example.com/john', $deserializedContact->getURL());
        $this->assertSame('4711', $deserializedContact->getIdReference('buyerId'));
        $this->assertSame('IT', $deserializedContact->getExtrinsicByName('department')?->value);
    }

    public function testGetContactByRole(): void
    {
        $buyer = Contact::create(new MultilanguageString('Buyer'), Contact::ROLE_BUYER);
        $endUser = Contact::create(new MultilanguageString('End User'), Contact::ROLE_ENDUSER);
        $secondBuyer = Contact::create(new MultilanguageString('Second Buyer'), Contact::ROLE_BUYER);

        $orderRequestHeader = $this->createOrderRequestHeader([$buyer, $endUser, $secondBuyer]);

        $this->assertSame($buyer, $orderRequestHeader->getContactByRole(Contact::ROLE_BUYER), 'first matching contact is returned');
        $this->assertSame($endUser, $orderRequestHeader->getContactByRole(Contact::ROLE_ENDUSER));
        $this->assertNull($orderRequestHeader->getContactByRole(Contact::ROLE_SALES));
    }

    public function testGetContactByRoleWithoutContacts(): void
    {
        $orderRequestHeader = $this->createOrderRequestHeader(null);

        $this->assertNull($orderRequestHeader->getContacts());
        $this->assertNull($orderRequestHeader->getContactByRole(Contact::ROLE_BUYER));
    }

    public function testAddContactInitializesContacts(): void
    {
        $orderRequestHeader = $this->createOrderRequestHeader(null);
        $contact = Contact::create(new MultilanguageString('Sales'), Contact::ROLE_SALES);

        $orderRequestHeader->addContact($contact);

        $this->assertSame([$contact], $orderRequestHeader->getContacts());
        $this->assertSame($contact, $orderRequestHeader->getContactByRole(Contact::ROLE_SALES));
    }

    public function testOrderRequestHeaderRejectsInvalidContacts(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createOrderRequestHeader(['not a contact']);
    }

    private function createOrderRequestHeader(?array $contacts): OrderRequestHeader
    {
        return OrderRequestHeader::create(
            'order-1',
            new DateTime('2025-01-01T00:00:00+00:00'),
            null,
            new BillTo(new Address(new MultilanguageString('Acme'))),
            new MoneyWrapper('EUR', 100),
            OrderRequestHeader::TYPE_NEW,
            null,
            $contacts,
        );
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity('933695160890', new DateTime('2023-01-23T16:00:06-01:00'));
    }
}
