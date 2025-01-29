<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\Contact;
use CXml\Model\MoneyWrapper;
use CXml\Model\OrganizationId;
use CXml\Model\ShipTo;
use CXml\Model\Trait\CommentsTrait;
use CXml\Model\Trait\ExtrinsicsTrait;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['organizationId', 'total', 'shipTo', 'contacts', 'comments', 'extrinsics'])]
class QuoteMessageHeader
{
    use CommentsTrait;
    use ExtrinsicsTrait;

    final public const TYPE_ACCEPT = 'accept';

    final public const TYPE_REJECT = 'reject';

    final public const TYPE_UPDATE = 'update';

    final public const TYPE_FINAL = 'final';

    final public const TYPE_AWARD = 'award';

    #[Serializer\SerializedName('ShipTo')]
    #[Serializer\XmlElement(cdata: false)]
    private ShipTo $shipTo;

    /**
     * @var Contact[]
     */
    #[Serializer\XmlList(entry: 'Contact', inline: true)]
    #[Serializer\Type('array<CXml\Model\Contact>')]
    private array $contacts = [];

    public function __construct(
        #[Serializer\SerializedName('OrganizationID')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly OrganizationId $organizationId,
        #[Serializer\SerializedName('Total')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly MoneyWrapper $total,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('type')]
        public readonly string $type,
        #[Serializer\SerializedName('quoteID')]
        #[Serializer\XmlAttribute]
        public readonly string $quoteId,
        #[Serializer\XmlAttribute]
        public readonly DateTimeInterface $quoteDate,
        #[Serializer\XmlAttribute]
        public readonly string $currency,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        public readonly string $lang = 'en',
    ) {
        Assertion::inArray($type, [
            self::TYPE_ACCEPT,
            self::TYPE_REJECT,
            self::TYPE_UPDATE,
            self::TYPE_FINAL,
            self::TYPE_AWARD,
        ]);
    }

    public function setShipTo(ShipTo $shipTo): self
    {
        $this->shipTo = $shipTo;

        return $this;
    }

    public function addContact(Contact $contact): self
    {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * @return Contact[]
     */
    public function getContacts(): array
    {
        return $this->contacts;
    }

    public function getShipTo(): ShipTo
    {
        return $this->shipTo;
    }
}
