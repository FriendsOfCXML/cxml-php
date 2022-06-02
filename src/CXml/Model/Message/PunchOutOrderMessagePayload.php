<?php

namespace CXml\Model\Message;

use CXml\Model\ItemIn;
use JMS\Serializer\Annotation as Ser;

class PunchOutOrderMessagePayload implements MessagePayloadInterface
{
	/**
	 * @Ser\SerializedName("BuyerCookie")
	 * @Ser\XmlElement (cdata=false)
	 */
	private string $buyerCookie;

	/**
	 * @Ser\SerializedName("PunchOutOrderMessageHeader")
	 */
	private PunchOutOrderMessageHeader $punchOutOrderMessageHeader;

	/**
	 * @Ser\XmlList(inline=true, entry="ItemIn")
	 * @Ser\Type("array<CXml\Model\ItemIn>")
	 *
	 * @var ItemIn[]
	 */
	private array $punchoutOrderMessageItems = [];

	public function __construct(string $buyerCookie, PunchOutOrderMessageHeader $punchOutOrderMessageHeader)
	{
		$this->buyerCookie = $buyerCookie;
		$this->punchOutOrderMessageHeader = $punchOutOrderMessageHeader;
	}

	public static function create(string $buyerCookie, PunchOutOrderMessageHeader $punchOutOrderMessageHeader): self
	{
		return new self($buyerCookie, $punchOutOrderMessageHeader);
	}

	public function getBuyerCookie(): string
	{
		return $this->buyerCookie;
	}

	public function getPunchOutOrderMessageHeader(): PunchOutOrderMessageHeader
	{
		return $this->punchOutOrderMessageHeader;
	}

	public function getPunchoutOrderMessageItems(): array
	{
		return $this->punchoutOrderMessageItems;
	}

	public function addPunchoutOrderMessageItem(ItemIn $punchoutOrderMessageItem): self
	{
		$this->punchoutOrderMessageItems[] = $punchoutOrderMessageItem;

		return $this;
	}
}
