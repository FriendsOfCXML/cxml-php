<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\RequestInterface;
use Mathielen\CXml\Model\ShipControl;
use Mathielen\CXml\Model\ShipNoticePortion;

class ShipNoticeRequest implements RequestInterface
{
	/**
	 * @Ser\SerializedName("ShipNoticeHeader")
	 */
	private ShipNoticeHeader $shipNoticeHeader;

	/**
	 * @Ser\XmlList(inline=true, entry="ShipControl")
	 * @Ser\Type("array<Mathielen\CXml\Model\ShipControl>")
	 *
	 * @var ShipControl[]
	 */
	private array $shipControls = [];

	/**
	 * @Ser\XmlList(inline=true, entry="ShipNoticePortion")
	 * @Ser\Type("array<Mathielen\CXml\Model\ShipNoticePortion>")
	 *
	 * @var ShipNoticePortion[]
	 */
	private array $shipNoticePortions = [];

	public function __construct(ShipNoticeHeader $shipNoticeHeader)
	{
		$this->shipNoticeHeader = $shipNoticeHeader;
	}

	public static function create(ShipNoticeHeader $shipNoticeHeader): self
	{
		return new self($shipNoticeHeader);
	}

	public function addShipControl(ShipControl $shipControl): self
	{
		$this->shipControls[] = $shipControl;

		return $this;
	}

	public function addShipNoticePortion(ShipNoticePortion $shipNoticePortion): self
	{
		$this->shipNoticePortions[] = $shipNoticePortion;

		return $this;
	}

	public function getShipNoticeHeader(): ShipNoticeHeader
	{
		return $this->shipNoticeHeader;
	}

	public function getShipControls(): array
	{
		return $this->shipControls;
	}

	public function getShipNoticePortions(): array
	{
		return $this->shipNoticePortions;
	}

	public function getCommentsAsString(): ?string
	{
		$commentStrings = [];

		if ($comments = $this->shipNoticeHeader->getComments()) {
			foreach ($comments as $comment) {
				$commentStrings[] = $comment->getValue();
			}
		}

		return empty($commentStrings) ? null : \implode("\n", $commentStrings);
	}
}
