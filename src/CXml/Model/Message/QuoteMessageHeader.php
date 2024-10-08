<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\CommentsTrait;
use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\MoneyWrapper;
use CXml\Model\OrganizationId;
use CXml\Model\ShipTo;
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
        private readonly OrganizationId $organizationId,
        #[Serializer\SerializedName('Total')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly MoneyWrapper $total,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('type')]
        private readonly string $type,
        #[Serializer\SerializedName('quoteID')]
        #[Serializer\XmlAttribute]
        private readonly string $quoteId,
        #[Serializer\XmlAttribute]
        private readonly DateTimeInterface $quoteDate,
        #[Serializer\XmlAttribute]
        private readonly string $currency,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        private readonly string $lang = 'en',
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

    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    /**
     * @return Contact[]
     */
    public function getContacts(): array
    {
        return $this->contacts;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getQuoteId(): string
    {
        return $this->quoteId;
    }

    public function getQuoteDate(): DateTimeInterface
    {
        return $this->quoteDate;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getTotal(): MoneyWrapper
    {
        return $this->total;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getShipTo(): ShipTo
    {
        return $this->shipTo;
    }
}
