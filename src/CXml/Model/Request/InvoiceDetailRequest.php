<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\InvoiceDetailHeaderOrder;
use CXml\Model\InvoiceDetailSummary;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['invoiceDetailRequestHeader', 'invoiceDetailHeaderOrders', 'invoiceDetailSummary'])]
class InvoiceDetailRequest implements RequestPayloadInterface
{
    /**
     * @var InvoiceDetailHeaderOrder[]
     */
    #[Serializer\XmlList(entry: 'InvoiceDetailHeaderOrder', inline: true)]
    #[Serializer\Type('array<CXml\Model\InvoiceDetailHeaderOrder>')]
    private array $invoiceDetailHeaderOrders = [];

    public function __construct(
        #[Serializer\SerializedName('InvoiceDetailRequestHeader')]
        public readonly InvoiceDetailRequestHeader $invoiceDetailRequestHeader,
        #[Serializer\SerializedName('InvoiceDetailSummary')]
        public readonly InvoiceDetailSummary $invoiceDetailSummary,
    ) {
    }

    public static function create(InvoiceDetailRequestHeader $invoiceDetailRequestHeader, InvoiceDetailSummary $invoiceDetailSummary): self
    {
        return new self($invoiceDetailRequestHeader, $invoiceDetailSummary);
    }

    public function addInvoiceDetailHeaderOrder(InvoiceDetailHeaderOrder $invoiceDetailHeaderOrder): self
    {
        $this->invoiceDetailHeaderOrders[] = $invoiceDetailHeaderOrder;

        return $this;
    }

    /**
     * @return InvoiceDetailHeaderOrder[]
     */
    public function getInvoiceDetailHeaderOrders(): array
    {
        return $this->invoiceDetailHeaderOrders;
    }
}
