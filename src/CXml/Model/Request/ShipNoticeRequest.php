<?php

namespace CXml\Model\Request;

use CXml\Model\ShipControl;
use CXml\Model\ShipNoticePortion;
use JMS\Serializer\Annotation as Serializer;

class ShipNoticeRequest implements RequestPayloadInterface
{
    #[Serializer\SerializedName('ShipNoticeHeader')]
    private ShipNoticeHeader $shipNoticeHeader;

    /**
     *
     * @var ShipControl[]
     */
    #[Serializer\XmlList(inline: true, entry: 'ShipControl')]
    #[Serializer\Type('array<CXml\Model\ShipControl>')]
    private array $shipControls = [];

    /**
     *
     * @var ShipNoticePortion[]
     */
    #[Serializer\XmlList(inline: true, entry: 'ShipNoticePortion')]
    #[Serializer\Type('array<CXml\Model\ShipNoticePortion>')]
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
