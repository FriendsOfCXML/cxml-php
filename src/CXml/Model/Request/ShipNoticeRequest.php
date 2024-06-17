<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\ShipControl;
use CXml\Model\ShipNoticePortion;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['shipNoticeHeader', 'shipControls', 'shipNoticePortions'])]
class ShipNoticeRequest implements RequestPayloadInterface
{
    /**
     * @var ShipControl[]
     */
    #[Serializer\XmlList(entry: 'ShipControl', inline: true)]
    #[Serializer\Type('array<CXml\Model\ShipControl>')]
    private array $shipControls = [];

    /**
     * @var ShipNoticePortion[]
     */
    #[Serializer\XmlList(entry: 'ShipNoticePortion', inline: true)]
    #[Serializer\Type('array<CXml\Model\ShipNoticePortion>')]
    private array $shipNoticePortions = [];

    public function __construct(#[Serializer\SerializedName('ShipNoticeHeader')]
        private readonly ShipNoticeHeader $shipNoticeHeader)
    {
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
