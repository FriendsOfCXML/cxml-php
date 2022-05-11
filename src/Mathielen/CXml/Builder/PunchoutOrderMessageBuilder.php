<?php

namespace Mathielen\CXml\Builder;

use Mathielen\CXml\Model\ItemDetail;
use Mathielen\CXml\Model\ItemId;
use Mathielen\CXml\Model\ItemIn;
use Mathielen\CXml\Model\Message\PunchOutOrderMessage;
use Mathielen\CXml\Model\Message\PunchOutOrderMessageHeader;
use Mathielen\CXml\Model\MoneyWrapper;
use Mathielen\CXml\Model\MultilanguageString;

// TODO not yet final and completed
class PunchoutOrderMessageBuilder
{
	private PunchOutOrderMessage $punchOutOrderMessage;

	private function __construct(string $buyerCookie, int $total, string $currency, ?string $operationAllowed)
	{
		$total = new MoneyWrapper($currency, $total);
		$punchoutOrderMessageHeader = new PunchOutOrderMessageHeader($total, $operationAllowed);
		$this->punchOutOrderMessage = PunchOutOrderMessage::create(
			$buyerCookie,
			$punchoutOrderMessageHeader
		);
	}

	public static function create(string $buyerCookie, int $total, string $currency, ?string $operationAllowed): self
	{
		return new self($buyerCookie, $total, $currency, $operationAllowed);
	}

	public function addPunchoutOrderMessageItem(string $sku, int $quantity, string $description, string $unitOfMeasure, int $unitPrice): self
	{
		$punchoutOrderMessageItem = ItemIn::create(
			$quantity,
			new ItemId($sku, null, $sku),
			new ItemDetail(new MultilanguageString($description, null, 'de'), $unitOfMeasure, new MoneyWrapper('EUR', $unitPrice)),
		);

		$this->punchOutOrderMessage->addPunchoutOrderMessageItem($punchoutOrderMessageItem);

		return $this;
	}

	public function build(): PunchOutOrderMessage
	{
		if (empty($this->punchOutOrderMessage->getPunchoutOrderMessageItems())) {
			throw new \RuntimeException('Cannot build PunchOutOrderMessage without any PunchoutOrderMessageItem');
		}

		return $this->punchOutOrderMessage;
	}
}
