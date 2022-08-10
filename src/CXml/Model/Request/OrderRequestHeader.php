<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\BillTo;
use CXml\Model\Comment;
use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
use CXml\Model\MoneyWrapper;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\SupplierOrderInfo;
use CXml\Model\Tax;
use JMS\Serializer\Annotation as Ser;

class OrderRequestHeader
{
	use ExtrinsicsTrait;
	use IdReferencesTrait;

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
	private ?ShipTo $shipTo = null;

	/**
	 * @Ser\XmlElement
	 * @Ser\SerializedName("BillTo")
	 */
	private BillTo $billTo;

	/**
	 * @Ser\XmlElement
	 * @Ser\SerializedName("Shipping")
	 */
	private ?Shipping $shipping = null;

	/**
	 * @Ser\XmlElement
	 * @Ser\SerializedName("Tax")
	 */
	private ?Tax $tax = null;

	/**
	 * @Ser\XmlList(inline=true, entry="Contact")
	 * @Ser\Type("array<CXml\Model\Contact>")
	 *
	 * @var Contact[]
	 */
	private ?array $contacts = null;

	/**
	 * @Ser\XmlList(inline=true, entry="Comments")
	 * @Ser\Type("array<CXml\Model\Comment>")
	 *
	 * @var Comment[]
	 */
	private ?array $comments = null;

	/**
	 * @Ser\SerializedName("SupplierOrderInfo")
	 */
	private ?SupplierOrderInfo $supplierOrderInfo = null;

	public function __construct(
		string $orderId,
		\DateTime $orderDate,
		?ShipTo $shipTo,
		BillTo $billTo,
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
		?ShipTo $shipTo,
		BillTo $billTo,
		MoneyWrapper $total,
		?array $comments = null,
		string $type = self::TYPE_NEW,
		?array $contacts = null
	): self {
		return new self($orderId, $orderDate, $shipTo, $billTo, $total, $comments, $type, $contacts);
	}

	public function getShipping(): ?Shipping
	{
		return $this->shipping;
	}

	public function setShipping(?Shipping $shipping): self
	{
		$this->shipping = $shipping;

		return $this;
	}

	public function getTax(): ?Tax
	{
		return $this->tax;
	}

	public function setTax(?Tax $tax): self
	{
		$this->tax = $tax;

		return $this;
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

	public function getShipTo(): ?ShipTo
	{
		return $this->shipTo;
	}

	public function getBillTo(): BillTo
	{
		return $this->billTo;
	}

	public function getComments(): ?array
	{
		return $this->comments;
	}

	public function addContact(Contact $contact): self
	{
		if (null === $this->contacts) {
			$this->contacts = [];
		}

		$this->contacts[] = $contact;

		return $this;
	}

	public function getContacts(): ?array
	{
		return $this->contacts;
	}

	public function getSupplierOrderInfo(): ?SupplierOrderInfo
	{
		return $this->supplierOrderInfo;
	}
}
