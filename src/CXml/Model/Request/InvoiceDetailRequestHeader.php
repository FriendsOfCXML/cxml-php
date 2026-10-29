<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\Comment;
use CXml\Model\InvoiceDetailHeaderIndicator;
use CXml\Model\InvoiceDetailLineIndicator;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['invoiceDetailHeaderIndicator', 'invoiceDetailLineIndicator', 'comments'])]
readonly class InvoiceDetailRequestHeader
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('invoiceID')]
        public string $invoiceId,
        #[Serializer\XmlAttribute]
        public DateTimeInterface $invoiceDate,
        #[Serializer\SerializedName('InvoiceDetailHeaderIndicator')]
        public InvoiceDetailHeaderIndicator $invoiceDetailHeaderIndicator,
        #[Serializer\SerializedName('InvoiceDetailLineIndicator')]
        public InvoiceDetailLineIndicator $invoiceDetailLineIndicator = new InvoiceDetailLineIndicator(),
        #[Serializer\SerializedName('Comments')]
        public ?Comment $comments = null,
    ) {
    }

    public static function create(string $invoiceId, DateTimeInterface $invoiceDate, InvoiceDetailHeaderIndicator $invoiceDetailHeaderIndicator, ?Comment $comments = null): self
    {
        return new self($invoiceId, $invoiceDate, $invoiceDetailHeaderIndicator, comments: $comments);
    }
}
