<?php

namespace Mathielen\CXml\Model\Request;

use Assert\Assertion;
use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\AddressWrapper;
use Mathielen\CXml\Model\Comment;
use Mathielen\CXml\Model\Contact;
use Mathielen\CXml\Model\MoneyWrapper;

class OrderRequestHeader
{
	public const TYPE_NEW = 'new';

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("orderID")
	 */
	private string $orderId;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("orderDate")
	 */
	private \DateTime $orderDate;

	/**
	 * @Ser\XmlAttribute
	 */
	private string $type = self::TYPE_NEW;

	/**
	 * @Ser\XmlElement
	 * @Ser\SerializedName("Total")
	 */
	private MoneyWrapper $total;

	/**
	 * @Ser\XmlElement
	 * @Ser\SerializedName("ShipTo")
	 */
	private ?AddressWrapper $shipTo;

	/**
	 * @Ser\XmlElement
	 * @Ser\SerializedName("BillTo")
	 */
	private AddressWrapper $billTo;

	/**
	 * @Ser\XmlList(inline=true, entry="Contact")
	 * @Ser\Type("array<Mathielen\CXml\Model\Contact>")
	 *
	 * @var Contact[]
	 */
	private ?array $contacts;

	/**
	 * @Ser\XmlList(inline=true, entry="Comments")
	 * @Ser\Type("array<Mathielen\CXml\Model\Comment>")
	 *
	 * @var Comment[]
	 */
	private ?array $comments;

	public function __construct(
		string $orderId,
		\DateTime $orderDate,
		?AddressWrapper $shipTo,
		AddressWrapper $billTo,
		MoneyWrapper $total,
		?array $comments = null,
		string $type = self::TYPE_NEW,
		?array $contacts = null
	) {
		if ($comments) {
			Assertion::allIsInstanceOf($comments, Comment::class);
		}
		if ($contacts) {
			Assertion::allIsInstanceOf($contacts, Contact::class);
		}

		$this->orderId = $orderId;
		$this->orderDate = $orderDate;
		$this->type = $type;
		$this->total = $total;
		$this->shipTo = $shipTo;
		$this->billTo = $billTo;
		$this->comments = $comments;
		$this->contacts = $contacts;
	}

	public static function create(
		string $orderId,
		\DateTime $orderDate,
		?AddressWrapper $shipTo,
		AddressWrapper $billTo,
		MoneyWrapper $total,
		?array $comments = null,
		string $type = self::TYPE_NEW,
		?array $contacts = null
	): self
	{
		return new self($orderId, $orderDate, $shipTo, $billTo, $total, $comments, $type, $contacts);
	}

	public function getOrderId(): string
	{
		return $this->orderId;
	}

	public function getOrderDate(): \DateTime
	{
		return $this->orderDate;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getTotal(): MoneyWrapper
	{
		return $this->total;
	}

	public function getShipTo(): ?AddressWrapper
	{
		return $this->shipTo;
	}

	public function getBillTo(): AddressWrapper
	{
		return $this->billTo;
	}

	public function getComments(): ?array
	{
		return $this->comments;
	}

	public function addContact(Contact $contact): self
	{
		if ($this->contacts === null) {
			$this->contacts = [];
		}

		$this->contacts[] = $contact;

		return $this;
	}
}
