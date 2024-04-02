<?php

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\CommentsTrait;
use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\MoneyWrapper;
use CXml\Model\OrganizationId;
use CXml\Model\ShipTo;
use JMS\Serializer\Annotation as Ser;

class QuoteMessageHeader
{
    use CommentsTrait;
    use ExtrinsicsTrait;

    public const TYPE_ACCEPT = 'accept';
    public const TYPE_REJECT = 'reject';
    public const TYPE_UPDATE = 'update';
    public const TYPE_FINAL = 'final';
    public const TYPE_AWARD = 'award';

    /**
     * @Ser\SerializedName("type")
     * @Ser\XmlAttribute
     */
    private string $type;

    /**
     * @Ser\SerializedName("quoteID")
     * @Ser\XmlAttribute
     */
    private string $quoteId;

    /**
     * @Ser\XmlAttribute
     */
    private \DateTimeInterface $quoteDate;

    /**
     * @Ser\XmlAttribute
     */
    private string $currency;

    /**
     * @Ser\XmlAttribute(namespace="http://www.w3.org/XML/1998/namespace")
     */
    private string $lang;

    /**
     * @Ser\SerializedName("OrganizationID")
     * @Ser\XmlElement (cdata=false)
     */
    private OrganizationId $organizationId;

    /**
     * @Ser\SerializedName("Total")
     * @Ser\XmlElement (cdata=false)
     */
    private MoneyWrapper $total;

    /**
     * @Ser\SerializedName("ShipTo")
     * @Ser\XmlElement (cdata=false)
     */
    private ShipTo $shipTo;

    /**
	 * @Ser\XmlList(inline=true, entry="Contact")
	 * @Ser\Type("array<CXml\Model\Contact>")
	 *
	 * @var Contact[]
	 */
    private array $contacts = [];

    public function __construct(OrganizationId $organizationId, MoneyWrapper $total, string $type, string $quoteId, \DateTime $quoteDate, string $currency, string $lang = 'en')
    {
        Assertion::inArray($type, [
            self::TYPE_ACCEPT,
            self::TYPE_REJECT,
            self::TYPE_UPDATE,
            self::TYPE_FINAL,
            self::TYPE_AWARD,
        ]);

        $this->organizationId = $organizationId;
        $this->total = $total;
        $this->type = $type;
        $this->quoteId = $quoteId;
        $this->quoteDate = $quoteDate;
        $this->currency = $currency;
        $this->lang = $lang;
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

	public function getQuoteDate(): \DateTimeInterface
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
