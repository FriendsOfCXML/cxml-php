<?php

namespace CXml\Builder;

use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\Comment;
use CXml\Model\Contact;
use CXml\Model\Description;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemOut;
use CXml\Model\Money;
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
	private \DateTime $orderDate;
	private int $total = 0;
	private string $currency;
	private array $comments = [];
	private array $contacts = [];
	private ?ShipTo $shipTo = null;
	private BillTo $billTo;
	private string $language;
	private ?Shipping $shipping = null;
	private ?Tax $tax = null;

	private function __construct(string $orderId, \DateTime $orderDate, string $currency, string $language = 'en')
	{
		$this->orderId = $orderId;
		$this->orderDate = $orderDate;
		$this->currency = $currency;
		$this->language = $language;
	}

	public static function create(string $orderId, \DateTime $orderDate, string $currency, string $language = 'en'): self
	{
		return new self($orderId, $orderDate, $currency, $language);
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

	public function shipping(int $costs, string $description = null): self
	{
		$this->shipping = new Shipping(
			$this->currency,
			$costs,
			new MultilanguageString($description, null, $this->language),
		);

		return $this;
	}

	public function tax(int $costs, string $description = null): self
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
		\DateTime $requestDeliveryDate = null
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
				)
			),
			$requestDeliveryDate
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

	private function buildOrderRequestHeader(): OrderRequestHeader
	{
		return OrderRequestHeader::create(
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
	}

	public function build(): OrderRequest
	{
		return OrderRequest::create(
			$this->buildOrderRequestHeader()
		)->addItems($this->items);
	}
}
