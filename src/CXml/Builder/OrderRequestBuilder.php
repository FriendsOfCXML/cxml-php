<?php

namespace CXml\Builder;

use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\Classification;
use CXml\Model\Comment;
use CXml\Model\Contact;
use CXml\Model\Description;
use CXml\Model\Extrinsic;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemOut;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PostalAddress;
use CXml\Model\Request\OrderRequest;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\Tax;
use CXml\Model\TransportInformation;

class OrderRequestBuilder
{
	private array $items = [];
	private string $orderId;
	private \DateTimeInterface $orderDate;
	private int $total = 0;
	private string $currency;
	private array $comments = [];
	private array $contacts = [];
	private ?ShipTo $shipTo = null;
	private BillTo $billTo;
	private string $language;
	private ?Shipping $shipping = null;
	private ?Tax $tax = null;
	private array $extrinsics = [];

	private function __construct(string $orderId, \DateTimeInterface $orderDate, string $currency, string $language = 'en')
	{
		$this->orderId = $orderId;
		$this->orderDate = $orderDate;
		$this->currency = $currency;
		$this->language = $language;
	}

	public static function create(string $orderId, \DateTimeInterface $orderDate, string $currency, string $language = 'en'): self
	{
		return new self($orderId, $orderDate, $currency, $language);
	}

	public static function fromPunchOutOrderMessage(
		PunchOutOrderMessage $punchOutOrderMessage,
		?string $currency = null,
		?string $orderId = null,
		?\DateTimeInterface $orderDate = null,
		string $language = 'en'
	): self {
		if ($supplierOrderInfo = $punchOutOrderMessage->getPunchOutOrderMessageHeader()->getSupplierOrderInfo()) {
			$orderId ??= $supplierOrderInfo->getOrderId();
			$orderDate ??= $supplierOrderInfo->getOrderDate();
		}
		$currency ??= $punchOutOrderMessage->getPunchOutOrderMessageHeader()->getTotal()->getMoney()->getCurrency();

		if (null === $orderId) {
			throw new \LogicException('orderId should either be given or present in the PunchOutOrderMessage');
		}
		if (null === $orderDate) {
			throw new \LogicException('orderDate should either be given or present in the PunchOutOrderMessage');
		}

		$orb = new self(
			$orderId,
			$orderDate,
			$currency,
			$language
		);

		$orb->setShipTo($punchOutOrderMessage->getPunchOutOrderMessageHeader()->getShipTo());

		foreach ($punchOutOrderMessage->getPunchoutOrderMessageItems() as $item) {
			$orb->addItem(
				$item->getQuantity(),
				$item->getItemId(),
				$item->getItemDetail()->getDescription()->getValue(),
				$item->getItemDetail()->getUnitOfMeasure(),
				$item->getItemDetail()->getUnitPrice()->getMoney()->getValueCent(),
				[
					new Classification('custom', '0'), // TODO make this configurable
				]
			);
		}

		return $orb;
	}

	public function billTo(
		string $name,
		PostalAddress $postalAddress = null,
		?string $addressId = null,
		?string $addressIdDomain = null,
		?string $email = null,
		?string $phone = null,
		?string $fax = null,
		?string $url = null
	): self {
		$this->billTo = new BillTo(
			new Address(
				new MultilanguageString($name, null, $this->language),
				$postalAddress,
				$addressId,
				$addressIdDomain,
				$email,
				$phone,
				$fax,
				$url
			)
		);

		return $this;
	}

	public function shipTo(
		string $name,
		PostalAddress $postalAddress,
		array $carrierIdentifiers = [],
		string $carrierAccountNo = null
	): self {
		$this->shipTo = new ShipTo(
			new Address(
				new MultilanguageString($name, null, $this->language),
				$postalAddress
			),
			$carrierAccountNo ? TransportInformation::fromContractAccountNumber($carrierAccountNo) : null
		);

		foreach ($carrierIdentifiers as $domain => $identifier) {
			$this->shipTo->addCarrierIdentifier($domain, $identifier);
		}

		return $this;
	}

	public function setShipTo(?ShipTo $shipTo): self
	{
		$this->shipTo = $shipTo;

		return $this;
	}

	public function shipping(int $costs, string $description): self
	{
		$this->shipping = new Shipping(
			$this->currency,
			$costs,
			new Description($description, null, $this->language),
		);

		return $this;
	}

	public function tax(int $costs, string $description): self
	{
		$this->tax = new Tax(
			$this->currency,
			$costs,
			new MultilanguageString($description, null, $this->language),
		);

		return $this;
	}

	public function addItem(
		int $quantity,
		ItemId $itemId,
		string $description,
		string $unitOfMeasure,
		int $unitPrice,
		array $classifications,
		\DateTimeInterface $requestDeliveryDate = null,
		ItemOut $parent = null
	): self {
		$lineNumber = \count($this->items) + 1;

		$item = ItemOut::create(
			$lineNumber,
			$quantity,
			$itemId,
			ItemDetail::create(
				new Description(
					$description,
					null,
					$this->language
				),
				$unitOfMeasure,
				new MoneyWrapper(
					$this->currency,
					$unitPrice
				),
				$classifications
			),
			$requestDeliveryDate,
			$parent ? $parent->getLineNumber() : null
		);

		$this->items[] = $item;
		$this->total += ($quantity * $unitPrice);

		return $this;
	}

	public function addComment(?string $value = null, ?string $type = null, ?string $lang = null, ?string $attachmentUrl = null): self
	{
		$this->comments[] = new Comment(
			$value,
			$type,
			$lang,
			$attachmentUrl
		);

		return $this;
	}

	public function addContact(string $name, string $email, string $role = Contact::ROLE_BUYER): self
	{
		$contact = new Contact(
			new MultilanguageString($name, null, $this->language),
			$role
		);
		$contact->addEmail($email);

		$this->contacts[] = $contact;

		return $this;
	}

	public function addExtrinsic(Extrinsic $extrinsic): self
	{
		$this->extrinsics[] = $extrinsic;

		return $this;
	}

	private function buildOrderRequestHeader(): OrderRequestHeader
	{
		$orh = OrderRequestHeader::create(
			$this->orderId,
			$this->orderDate,
			$this->shipTo,
			$this->billTo,
			new MoneyWrapper($this->currency, $this->total),
			$this->comments,
			OrderRequestHeader::TYPE_NEW,
			$this->contacts
		)
			->setShipping($this->shipping)
			->setTax($this->tax)
		;

		foreach ($this->extrinsics as $extrinsic) {
			$orh->addExtrinsic($extrinsic);
		}

		return $orh;
	}

	public function build(): OrderRequest
	{
		if (!isset($this->billTo)) {
			throw new \LogicException('BillTo is required');
		}

		return OrderRequest::create(
			$this->buildOrderRequestHeader()
		)->addItems($this->items);
	}

	/**
	 * @return ItemOut[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
}
