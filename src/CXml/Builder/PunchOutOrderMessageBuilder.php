<?php

namespace CXml\Builder;

use CXml\Model\Address;
use CXml\Model\Description;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemIn;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PostalAddress;
use CXml\Model\PriceBasisQuantity;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\Tax;
use CXml\Model\TransportInformation;

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
    private ?\DateTimeInterface $orderDate;
    private ?ShipTo $shipTo = null;

    private function __construct(string $language, string $buyerCookie, string $currency, string $operationAllowed = null)
    {
        $this->buyerCookie = $buyerCookie;
        $this->currency = $currency;
        $this->operationAllowed = $operationAllowed;
        $this->language = $language;
    }

    public static function create(string $language, string $buyerCookie, string $currency, string $operationAllowed = null): self
    {
        return new self($language, $buyerCookie, $currency, $operationAllowed);
    }

    public function orderReference(string $orderId, \DateTimeInterface $orderDate = null): self
    {
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;

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

        return $this;
    }

    public function addPunchoutOrderMessageItem(
        ItemId $itemId,
        int $quantity,
        string $description,
        string $unitOfMeasure,
        int $unitPrice,
        array $classifications,
        string $manufacturerPartId = null,
        string $manufacturerName = null,
        int $leadTime = null,
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
            ),
            $classifications
        )
            ->setManufacturerPartId($manufacturerPartId)
            ->setManufacturerName($manufacturerName)
            ->setLeadtime($leadTime)
        ;

        if ($extrinsics) {
            foreach ($extrinsics as $k => $v) {
                $itemDetail->addExtrinsicAsKeyValue($k, $v);
            }
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

        $moneyValueCent = $itemIn->getItemDetail()->getUnitPrice()->getMoney()->getValueCent();
        $itemQty = $itemIn->getQuantity();

        if (
            $itemIn->getItemDetail()->getPriceBasisQuantity() instanceof PriceBasisQuantity
            && $itemIn->getItemDetail()->getPriceBasisQuantity()->getQuantity() > 0
        ) {
            $priceBasisQuantity = $itemIn->getItemDetail()->getPriceBasisQuantity();
            $this->total += (int) \round($itemQty * ($priceBasisQuantity->getConversionFactor() / $priceBasisQuantity->getQuantity()) * $moneyValueCent);
        } else {
            $this->total += $moneyValueCent * $itemQty;
        }

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

        if (isset($this->shipTo)) {
            $punchoutOrderMessageHeader->setShipTo($this->shipTo);
        }

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
