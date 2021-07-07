<?php

namespace Mathielen\CXml\Builder;

use Mathielen\CXml\Model\AddressWrapper;
use Mathielen\CXml\Model\Comment;
use Mathielen\CXml\Model\ItemDetail;
use Mathielen\CXml\Model\ItemId;
use Mathielen\CXml\Model\ItemOut;
use Mathielen\CXml\Model\MoneyWrapper;
use Mathielen\CXml\Model\MultilanguageString;
use Mathielen\CXml\Model\PostalAddress;
use Mathielen\CXml\Model\Request\OrderRequest;
use Mathielen\CXml\Model\Request\OrderRequestHeader;

class OrderRequestBuilder
{
    private array $items = [];
    private string $orderId;
    private \DateTime $orderDate;
    private int $total = 0;
    private string $currency;
    private array $comments = [];
    private ?AddressWrapper $shipTo = null;
    private AddressWrapper $billTo;
    private string $language;

    public function __construct(string $orderId, \DateTime $orderDate, string $currency, string $language = 'en')
    {
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->currency = $currency;
        $this->language = $language;
    }

    public static function create(string $orderId, \DateTime $orderDate, string $currency, string $language = 'en'): self
    {
        return new self($orderId, $orderDate, $currency, $language);
    }

    public function billTo(
        string $name,
        PostalAddress $postalAddress = null,
        ?string $addressId = null,
        ?string $addressIdDomain = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $fax = null,
        ?string $url = null
    ): self
    {
        $this->billTo = new AddressWrapper(
            new MultilanguageString($name, $this->language),
            $postalAddress,
            $addressId,
            $addressIdDomain,
            $email,
            $phone,
            $fax,
            $url
        );

        return $this;
    }

    public function shipTo(string $name, PostalAddress $postalAddress = null, ?string $addressId = null, ?string $addressIdDomain = null, ?string $email = null, ?string $phone = null, ?string $fax = null, ?string $url = null): self
    {
        $this->shipTo = new AddressWrapper(
            new MultilanguageString($name, $this->language),
            $postalAddress,
            $addressId,
            $addressIdDomain,
            $email,
            $phone,
            $fax,
            $url
        );

        return $this;
    }

    public function build(): OrderRequest
    {
        return OrderRequest::create(
            $this->buildOrderRequestHeader()
        )->addItems($this->items);
    }

    public function addItem(
        int $quantity,
        string $supplierPartId,
        string $description,
        string $unitOfMeasure,
        int $unitPrice
    ): self
    {
        $lineNumber = count($this->items)+1;

        $item = ItemOut::create(
            $lineNumber,
            $quantity,
            new ItemId($supplierPartId),
            new ItemDetail(
                new MultilanguageString(
                    $description,
                    $this->language
                ),
                $unitOfMeasure,
                new MoneyWrapper(
                    $this->currency,
                    $unitPrice
                )
            )
        );

        $this->items[] = $item;
        $this->total += ($quantity * $unitPrice);

        return $this;
    }

    public function addComment(?string $value = null, ?string $attachmentUrl = null): self
    {
        $this->comments[] = new Comment(
            $value,
            $attachmentUrl
        );

        return $this;
    }

    private function buildOrderRequestHeader(): OrderRequestHeader
    {
        return new OrderRequestHeader(
            $this->orderId,
            $this->orderDate,
            $this->shipTo,
            $this->billTo,
            $this->comments,
            new MoneyWrapper($this->currency, $this->total)
        );
    }
}
