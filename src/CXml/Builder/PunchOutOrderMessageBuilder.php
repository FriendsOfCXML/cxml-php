<?php

namespace CXml\Builder;

use CXml\Model\Classification;
use CXml\Model\Description;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemIn;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\Shipping;
use CXml\Model\Tax;

class PunchOutOrderMessageBuilder
{
	private string $buyerCookie;
	private string $currency;
	private ?string $operationAllowed;
	private string $language;

	/**
	 * @var ItemIn[]
	 */
	private array $punchoutOrderMessageItems = [];
	private int $total = 0;
	private ?Shipping $shipping = null;
	private ?Tax $tax = null;
	private string $orderId;
	private ?\DateTime $orderDate;

	private function __construct(string $language, string $buyerCookie, string $currency, ?string $operationAllowed = null)
	{
		$this->buyerCookie = $buyerCookie;
		$this->currency = $currency;
		$this->operationAllowed = $operationAllowed;
		$this->language = $language;
	}

	public static function create(string $language, string $buyerCookie, string $currency, ?string $operationAllowed = null): self
	{
		return new self($language, $buyerCookie, $currency, $operationAllowed);
	}

	public function orderReference(string $orderId, \DateTime $orderDate = null): self
	{
		$this->orderId = $orderId;
		$this->orderDate = $orderDate;

		return $this;
	}

	public function shipping(int $shipping, string $taxDescription): self
	{
		$this->shipping = new Shipping(
			$this->currency,
			$shipping,
			new Description(
				$taxDescription,
				null,
				$this->language
			)
		);

		$this->total += $shipping;

		return $this;
	}

	public function tax(int $tax, string $taxDescription): self
	{
		$this->tax = new Tax(
			$this->currency,
			$tax,
			new Description(
				$taxDescription,
				null,
				$this->language
			)
		);

		$this->total += $tax;

		return $this;
	}

	public function addPunchoutOrderMessageItem(
		ItemId $itemId,
		int $quantity,
		string $description,
		string $unitOfMeasure,
		int $unitPrice,
		array $classifications,
		?string $manufacturerPartId = null,
		?string $manufacturerName = null,
		?int $leadTime = null,
		array $extrinsics = null
	): self {
		$itemDetail = ItemDetail::create(
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
		)
			->setManufacturerPartId($manufacturerPartId)
			->setManufacturerName($manufacturerName)
			->setLeadtime($leadTime)
		;

		if ($extrinsics) {
			foreach ($extrinsics as $k => $v) {
				$itemDetail->addExtrinsic($k, $v);
			}
		}

		foreach ($classifications as $k => $v) {
			$itemDetail->addClassification(new Classification($k, $v));
		}

		$punchoutOrderMessageItem = ItemIn::create(
			$quantity,
			$itemId,
			$itemDetail
		);

		return $this->addItem($punchoutOrderMessageItem);
	}

	public function addItem(ItemIn $itemIn): self
	{
		$this->punchoutOrderMessageItems[] = $itemIn;
		$this->total += ($itemIn->getItemDetail()->getUnitPrice()->getMoney()->getValueCent() * $itemIn->getQuantity());

		return $this;
	}

	public function build(): PunchOutOrderMessage
	{
		if (empty($this->punchoutOrderMessageItems)) {
			throw new \RuntimeException('Cannot build PunchOutOrderMessage without any PunchoutOrderMessageItem');
		}

		$punchoutOrderMessageHeader = new PunchOutOrderMessageHeader(
			new MoneyWrapper($this->currency, $this->total),
			$this->shipping,
			$this->tax,
			$this->operationAllowed
		);

		if (isset($this->orderId)) {
			$punchoutOrderMessageHeader->setSupplierOrderInfo($this->orderId, $this->orderDate);
		}

		$punchOutOrderMessage = PunchOutOrderMessage::create(
			$this->buyerCookie,
			$punchoutOrderMessageHeader
		);

		foreach ($this->punchoutOrderMessageItems as $punchoutOrderMessageItem) {
			$punchOutOrderMessage->addPunchoutOrderMessageItem($punchoutOrderMessageItem);
		}

		return $punchOutOrderMessage;
	}
}
