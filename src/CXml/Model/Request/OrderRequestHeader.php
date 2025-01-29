<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\BillTo;
use CXml\Model\BusinessPartner;
use CXml\Model\CommentsTrait;
use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
use CXml\Model\MoneyWrapper;
use CXml\Model\Payment;
use CXml\Model\PaymentTerm;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\SupplierOrderInfo;
use CXml\Model\Tax;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['total', 'shipTo', 'billTo', 'businessPartners', 'shipping', 'tax', 'payment', 'paymentTerm', 'contacts', 'comments', 'supplierOrderInfo', 'idReferences', 'extrinsics'])]
class OrderRequestHeader
{
    use CommentsTrait;
    use IdReferencesTrait;
    use ExtrinsicsTrait;

    final public const TYPE_NEW = 'new';

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('Shipping')]
    private ?Shipping $shipping = null;

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('Tax')]
    private ?Tax $tax = null;

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('Payment')]
    private ?Payment $payment = null;

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('PaymentTerm')]
    private ?PaymentTerm $paymentTerm = null;

    #[Serializer\SerializedName('SupplierOrderInfo')]
    private ?SupplierOrderInfo $supplierOrderInfo = null;

    #[Serializer\XmlList(entry: 'BusinessPartner', inline: true)]
    #[Serializer\Type('array<CXml\Model\BusinessPartner>')]
    private array $businessPartners;

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('ShipTo')]
    private ?ShipTo $shipTo = null; /* cant be 'readonly' bc must be initialized with null -> jms deserialization */

    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('orderID')]
        private readonly string $orderId,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('orderDate')]
        private readonly DateTimeInterface $orderDate,
        ?ShipTo $shipTo, /* cant be 'readonly' bc must be initialized with null -> jms deserialization */
        #[Serializer\XmlElement]
        #[Serializer\SerializedName('BillTo')]
        private readonly BillTo $billTo,
        #[Serializer\XmlElement]
        #[Serializer\SerializedName('Total')]
        private readonly MoneyWrapper $total,
        #[Serializer\XmlAttribute]
        private readonly string $type = self::TYPE_NEW,
        #[Serializer\XmlAttribute]
        private readonly ?DateTimeInterface $requestedDeliveryDate = null,
        #[Serializer\Type('array<CXml\Model\Contact>')]
        #[Serializer\XmlList(entry: 'Contact', inline: true)]
        private ?array $contacts = null,
    ) {
        $this->shipTo = $shipTo;

        if (null !== $contacts) {
            Assertion::allIsInstanceOf($contacts, Contact::class);
        }
    }

    public static function create(
        string $orderId,
        DateTimeInterface $orderDate,
        ?ShipTo $shipTo,
        BillTo $billTo,
        MoneyWrapper $total,
        string $type = self::TYPE_NEW,
        ?DateTimeInterface $requestedDeliveryDate = null,
        ?array $contacts = null,
    ): self {
        return new self($orderId, $orderDate, $shipTo, $billTo, $total, $type, $requestedDeliveryDate, $contacts);
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    public function setShipping(?Shipping $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function getTax(): ?Tax
    {
        return $this->tax;
    }

    public function setTax(?Tax $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getOrderDate(): DateTimeInterface
    {
        return $this->orderDate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTotal(): MoneyWrapper
    {
        return $this->total;
    }

    public function getShipTo(): ?ShipTo
    {
        return $this->shipTo;
    }

    public function getBillTo(): BillTo
    {
        return $this->billTo;
    }

    public function addContact(Contact $contact): self
    {
        if (null === $this->contacts) {
            $this->contacts = [];
        }

        $this->contacts[] = $contact;

        return $this;
    }

    public function getContacts(): ?array
    {
        return $this->contacts;
    }

    public function getSupplierOrderInfo(): ?SupplierOrderInfo
    {
        return $this->supplierOrderInfo;
    }

    public function addBusinessPartner(BusinessPartner $businessPartner): self
    {
        $this->businessPartners[] = $businessPartner;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getPaymentTerm(): ?PaymentTerm
    {
        return $this->paymentTerm;
    }

    public function setPaymentTerm(?PaymentTerm $paymentTerm): self
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }
}
