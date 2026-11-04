<?php

declare(strict_types=1);

namespace CXml\Model;

use Assert\Assertion;
use CXml\Model\Trait\CommentsTrait;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Status of an InvoiceDetailRequest, used inside a StatusUpdateRequest.
 *
 * DTD: <!ELEMENT InvoiceStatus (InvoiceIDInfo?, PartialAmount?, Comments*)>
 */
#[Serializer\AccessorOrder(order: 'custom', custom: ['type', 'paymentNetDueDate', 'invoiceIdInfo', 'partialAmount', 'comments'])]
class InvoiceStatus
{
    use CommentsTrait;

    final public const TYPE_PROCESSING = 'processing';

    final public const TYPE_CANCELED = 'canceled';

    final public const TYPE_RECONCILED = 'reconciled';

    final public const TYPE_REJECTED = 'rejected';

    final public const TYPE_PAYING = 'paying';

    final public const TYPE_PAID = 'paid';

    public function __construct(
        #[Serializer\XmlAttribute]
        public readonly string $type,
        #[Serializer\SerializedName('InvoiceIDInfo')]
        public readonly ?InvoiceIDInfo $invoiceIdInfo = null,
        /**
         * Only relevant when the status type is "paid".
         */
        #[Serializer\SerializedName('PartialAmount')]
        public readonly ?MoneyWrapper $partialAmount = null,
        #[Serializer\XmlAttribute]
        public readonly ?DateTimeInterface $paymentNetDueDate = null,
    ) {
        Assertion::inArray($type, [
            self::TYPE_PROCESSING,
            self::TYPE_CANCELED,
            self::TYPE_RECONCILED,
            self::TYPE_REJECTED,
            self::TYPE_PAYING,
            self::TYPE_PAID,
        ]);
    }

    public static function create(string $type, ?InvoiceIDInfo $invoiceIdInfo = null, ?MoneyWrapper $partialAmount = null, ?DateTimeInterface $paymentNetDueDate = null): self
    {
        return new self($type, $invoiceIdInfo, $partialAmount, $paymentNetDueDate);
    }
}
