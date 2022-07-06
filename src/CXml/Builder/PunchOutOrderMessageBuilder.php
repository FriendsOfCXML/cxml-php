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
	private ?int $shipping = null;
	private ?int $tax = null;
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

	public function shipping(?int $shipping): self
	{
		$this->shipping = $shipping;

		if (\is_int($shipping)) {
			$this->total += $shipping;
		}

		return $this;
	}

	public function tax(?int $tax): self
	{
		$this->tax = $tax;

		if (\is_int($tax)) {
			$this->total += $tax;
		}

		return $this;
	}

	public function addPunchoutOrderMessageItem(
		string $sku,
		int $quantity,
		string $description,
		string $unitOfMeasure,
		int $unitPrice,
		array $classifications,
		?string $manufacturerPartId = null,
		?string $manufacturerName = null,
		?int $leadTime = null
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

		foreach ($classifications as $k => $v) {
			$itemDetail->addClassification(new Classification($k, $v));
		}

		$punchoutOrderMessageItem = ItemIn::create(
			$quantity,
			new ItemId($sku, null, $sku),
			$itemDetail
		);

		$this->punchoutOrderMessageItems[] = $punchoutOrderMessageItem;
		$this->total += ($unitPrice * $quantity);

		return $this;
	}

	public function build(): PunchOutOrderMessage
	{
		if (empty($this->punchoutOrderMessageItems)) {
			throw new \RuntimeException('Cannot build PunchOutOrderMessage without any PunchoutOrderMessageItem');
		}

		$punchoutOrderMessageHeader = new PunchOutOrderMessageHeader(
			new MoneyWrapper($this->currency, $this->total),
			$this->shipping ? new MoneyWrapper($this->currency, $this->shipping) : null,
			$this->tax ? new MoneyWrapper($this->currency, $this->tax) : null,
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
