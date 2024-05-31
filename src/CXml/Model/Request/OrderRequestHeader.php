<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\BillTo;
use CXml\Model\CommentsTrait;
use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
use CXml\Model\MoneyWrapper;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\SupplierOrderInfo;
use CXml\Model\Tax;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['total', 'shipTo', 'billTo', 'shipping', 'tax', 'contacts', 'comments', 'supplierOrderInfo', 'idReferences', 'extrinsics'])]
class OrderRequestHeader
{
    use CommentsTrait;
    use IdReferencesTrait;
    use ExtrinsicsTrait;

    public const TYPE_NEW = 'new';

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('Shipping')]
    private ?Shipping $shipping = null;

    #[Serializer\XmlElement]
    #[Serializer\SerializedName('Tax')]
    private ?Tax $tax = null;

    /**
     * @var Contact[]
     */
    #[Serializer\XmlList(entry: 'Contact', inline: true)]
    #[Serializer\Type('array<CXml\Model\Contact>')]
    private ?array $contacts = null;

    #[Serializer\SerializedName('SupplierOrderInfo')]
    private ?SupplierOrderInfo $supplierOrderInfo = null;

    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('orderID')]
        private string $orderId,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('orderDate')]
        private \DateTimeInterface $orderDate,
        #[Serializer\XmlElement]
        #[Serializer\SerializedName('ShipTo')]
        private ?ShipTo $shipTo,
        #[Serializer\XmlElement]
        #[Serializer\SerializedName('BillTo')]
        private BillTo $billTo,
        #[Serializer\XmlElement]
        #[Serializer\SerializedName('Total')]
        private MoneyWrapper $total,
        #[Serializer\XmlAttribute]
        private string $type = self::TYPE_NEW,
        array $contacts = null
    ) {
        if ($contacts) {
            Assertion::allIsInstanceOf($contacts, Contact::class);
        }

        $this->contacts = $contacts;
    }

    public static function create(
        string $orderId,
        \DateTimeInterface $orderDate,
        ?ShipTo $shipTo,
        BillTo $billTo,
        MoneyWrapper $total,
        string $type = self::TYPE_NEW,
        array $contacts = null
    ): self {
        return new self($orderId, $orderDate, $shipTo, $billTo, $total, $type, $contacts);
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

    public function getOrderDate(): \DateTimeInterface
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
}
