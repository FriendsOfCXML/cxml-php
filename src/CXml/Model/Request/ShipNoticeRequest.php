<?php

namespace CXml\Model\Request;

use CXml\Model\ShipControl;
use CXml\Model\ShipNoticePortion;
use JMS\Serializer\Annotation as Ser;

class ShipNoticeRequest implements RequestPayloadInterface
{
    /**
     * @Ser\SerializedName("ShipNoticeHeader")
     */
    private ShipNoticeHeader $shipNoticeHeader;

    /**
     * @Ser\XmlList(inline=true, entry="ShipControl")
     * @Ser\Type("array<CXml\Model\ShipControl>")
     *
     * @var ShipControl[]
     */
    private array $shipControls = [];

    /**
     * @Ser\XmlList(inline=true, entry="ShipNoticePortion")
     * @Ser\Type("array<CXml\Model\ShipNoticePortion>")
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
        return $this->shipNoticeHeader->getCommentsAsString();
    }
}
