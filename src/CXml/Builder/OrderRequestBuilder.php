<?php

declare(strict_types=1);

namespace CXml\Builder;

use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\BusinessPartner;
use CXml\Model\Classification;
use CXml\Model\Comment;
use CXml\Model\Contact;
use CXml\Model\Description;
use CXml\Model\ExtensionInterface;
use CXml\Model\Extrinsic;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemOut;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\Payment;
use CXml\Model\Phone;
use CXml\Model\PostalAddress;
use CXml\Model\PriceBasisQuantity;
use CXml\Model\Request\OrderRequest;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\SupplierOrderInfo;
use CXml\Model\Tax;
use CXml\Model\TaxDetail;
use CXml\Model\TransportInformation;
use DateTimeInterface;
use LogicException;

use function count;
use function round;

class OrderRequestBuilder
{
    private array $items = [];

    private int $total = 0;

    private array $comments = [];

    private array $contacts = [];

    private ?ShipTo $shipTo = null;

    private BillTo $billTo;

    private ?Shipping $shipping = null;

    private ?Tax $tax = null;

    private array $extrinsics = [];

    private array $businessPartners = [];

    private ?Payment $payment = null;

    private function __construct(
        private readonly string $orderId,
        private readonly DateTimeInterface $orderDate,
        private readonly string $currency,
        private readonly string $language = 'en',
        private readonly ?DateTimeInterface $requestedDeliveryDate = null,
    ) {
    }

    public static function create(
        string $orderId,
        DateTimeInterface $orderDate,
        string $currency,
        string $language = 'en',
        ?DateTimeInterface $requestedDeliveryDate = null,
    ): self {
        return new self($orderId, $orderDate, $currency, $language, $requestedDeliveryDate);
    }

    public static function fromPunchOutOrderMessage(
        PunchOutOrderMessage $punchOutOrderMessage,
        ?string $currency = null,
        ?string $orderId = null,
        ?DateTimeInterface $orderDate = null,
        string $language = 'en',
    ): self {
        if (($supplierOrderInfo = $punchOutOrderMessage->punchOutOrderMessageHeader->getSupplierOrderInfo()) instanceof SupplierOrderInfo) {
            $orderId ??= $supplierOrderInfo->orderId;
            $orderDate ??= $supplierOrderInfo->orderDate;
        }

        $currency ??= $punchOutOrderMessage->punchOutOrderMessageHeader->total->money->currency;

        if (null === $orderId) {
            throw new LogicException('orderId should either be given or present in the PunchOutOrderMessage');
        }

        if (!$orderDate instanceof DateTimeInterface) {
            throw new LogicException('orderDate should either be given or present in the PunchOutOrderMessage');
        }

        $orb = new self(
            $orderId,
            $orderDate,
            $currency,
            $language,
            null,
        );

        $orb->setShipTo($punchOutOrderMessage->punchOutOrderMessageHeader->getShipTo());

        foreach ($punchOutOrderMessage->getPunchoutOrderMessageItems() as $item) {
            $orb->addItem(
                $item->quantity,
                $item->itemId,
                $item->itemDetail->description->value,
                $item->itemDetail->unitOfMeasure,
                $item->itemDetail->unitPrice->money->getValueCent(),
                [
                    new Classification('custom', '0'), // TODO make this configurable
                ],
            );
        }

        return $orb;
    }

    public function billTo(
        string $name,
        ?PostalAddress $postalAddress = null,
        ?string $addressId = null,
        ?string $addressIdDomain = null,
        ?string $email = null,
        ?Phone $phone = null,
        ?string $fax = null,
        ?string $url = null,
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
                $url,
            ),
        );

        return $this;
    }

    public function shipTo(
        string $name,
        PostalAddress $postalAddress,
        array $carrierIdentifiers = [],
        ?string $carrierAccountNo = null,
        ?string $carrierShippingMethod = null,
    ): self {
        $transportInformation = null;
        if (null !== $carrierAccountNo || null != $carrierShippingMethod) {
            $transportInformation = TransportInformation::create($carrierAccountNo, $carrierShippingMethod);
        }

        $this->shipTo = new ShipTo(
            new Address(
                new MultilanguageString($name, null, $this->language),
                $postalAddress,
            ),
            $transportInformation,
        );

        foreach ($carrierIdentifiers as $domain => $identifier) {
            $this->shipTo->addCarrierIdentifier($domain, $identifier);
        }

        return $this;
    }

    public function setShipTo(?ShipTo $shipTo): self
    {
        $this->shipTo = $shipTo;

        return $this;
    }

    public function shipping(int $costs, string $description): self
    {
        $this->shipping = new Shipping(
            $this->currency,
            $costs,
            new Description($description, null, $this->language),
        );

        return $this;
    }

    public function tax(int $costs, string $description, array $taxDetails = []): self
    {
        $this->tax = new Tax(
            $this->currency,
            $costs,
            new MultilanguageString($description, null, $this->language),
        );

        foreach ($taxDetails as $taxDetail) {
            $this->tax->addTaxDetail(
                new TaxDetail(
                    $taxDetail['category'],
                    new MoneyWrapper($this->currency, $taxDetail['amount']),
                    $taxDetail['rate'],
                    $taxDetail['type'],
                ),
            );
        }

        return $this;
    }

    public function addItem(
        int $quantity,
        ItemId $itemId,
        string $description,
        string $unitOfMeasure,
        int $unitPrice,
        array $classifications,
        ?DateTimeInterface $requestDeliveryDate = null,
        ?ItemOut $parent = null,
        ?PriceBasisQuantity $priceBasisQuantity = null,
    ): ItemOut {
        $lineNumber = count($this->items) + 1;

        $item = ItemOut::create(
            $lineNumber,
            $quantity,
            $itemId,
            ItemDetail::create(
                new Description(
                    $description,
                    null,
                    $this->language,
                ),
                $unitOfMeasure,
                new MoneyWrapper(
                    $this->currency,
                    $unitPrice,
                ),
                $classifications,
                $priceBasisQuantity,
            ),
            $requestDeliveryDate,
            $parent instanceof ItemOut ? $parent->lineNumber : null,
        );

        $this->items[] = $item;

        if ($priceBasisQuantity instanceof PriceBasisQuantity && $priceBasisQuantity->quantity > 0) {
            $this->total += (int)round($quantity * ($priceBasisQuantity->conversionFactor / $priceBasisQuantity->quantity) * $unitPrice);
        } else {
            $this->total += ($quantity * $unitPrice);
        }

        return $item;
    }

    public function addComment(?string $value = null, ?string $type = null, ?string $lang = null, ?string $attachmentUrl = null): self
    {
        $this->comments[] = new Comment(
            $value,
            $type,
            $lang,
            $attachmentUrl,
        );

        return $this;
    }

    public function addContact(string $name, string $email, string $role = Contact::ROLE_BUYER): self
    {
        $contact = new Contact(
            new MultilanguageString($name, null, $this->language),
            $role,
        );
        $contact->addEmail($email);

        $this->contacts[] = $contact;

        return $this;
    }

    public function addExtrinsic(string $key, string $value): self
    {
        $this->extrinsics[] = new Extrinsic($key, $value);

        return $this;
    }

    private function buildOrderRequestHeader(): OrderRequestHeader
    {
        $orh = OrderRequestHeader::create(
            $this->orderId,
            $this->orderDate,
            $this->shipTo,
            $this->billTo,
            new MoneyWrapper($this->currency, $this->total),
            OrderRequestHeader::TYPE_NEW,
            $this->requestedDeliveryDate,
            $this->contacts,
        )
            ->setShipping($this->shipping)
            ->setPayment($this->payment)
            ->setTax($this->tax);

        foreach ($this->comments as $comment) {
            $orh->addComment($comment);
        }

        foreach ($this->extrinsics as $extrinsic) {
            $orh->addExtrinsic($extrinsic);
        }

        foreach ($this->businessPartners as $businessPartner) {
            $orh->addBusinessPartner($businessPartner);
        }

        return $orh;
    }

    public function build(): OrderRequest
    {
        if (!isset($this->billTo)) {
            throw new LogicException('BillTo is required');
        }

        return OrderRequest::create(
            $this->buildOrderRequestHeader(),
        )->addItems($this->items);
    }

    /**
     * @return ItemOut[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addBusinessPartner(string $role, string $name, array $idReferences = []): void
    {
        $bp = new BusinessPartner(
            $role,
            new Address(
                new MultilanguageString($name, null, $this->language),
            ),
        );

        foreach ($idReferences as $domain => $identifier) {
            $bp->addIdReference($domain, $identifier);
        }

        $this->businessPartners[] = $bp;
    }

    public function setPayment(ExtensionInterface $payment): self
    {
        $this->payment = new Payment($payment);

        return $this;
    }
}
