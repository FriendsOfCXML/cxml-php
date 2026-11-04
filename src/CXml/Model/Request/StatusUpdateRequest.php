<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\DocumentReference;
use CXml\Model\DocumentStatus;
use CXml\Model\InvoiceStatus;
use CXml\Model\Status;
use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

/**
 * DTD: <!ELEMENT StatusUpdateRequest (DocumentReference?, Status, (PaymentStatus | SourcingStatus | InvoiceStatus | DocumentStatus | IntegrationStatus)?, Extrinsic*)>.
 */
#[Serializer\AccessorOrder(order: 'custom', custom: ['documentReference', 'status', 'invoiceStatus', 'documentStatus', 'extrinsics'])]
class StatusUpdateRequest implements RequestPayloadInterface
{
    use ExtrinsicsTrait;

    #[Serializer\SerializedName('DocumentReference')]
    public readonly ?DocumentReference $documentReference;

    public function __construct(
        #[Serializer\SerializedName('Status')]
        public readonly Status $status,
        ?string $documentReference = null,
        #[Serializer\SerializedName('DocumentStatus')]
        public readonly ?DocumentStatus $documentStatus = null,
        #[Serializer\SerializedName('InvoiceStatus')]
        public readonly ?InvoiceStatus $invoiceStatus = null,
    ) {
        Assertion::false(
            $documentStatus instanceof DocumentStatus && $invoiceStatus instanceof InvoiceStatus,
            'StatusUpdateRequest may contain at most one of DocumentStatus or InvoiceStatus.',
        );

        $this->documentReference = null !== $documentReference && '' !== $documentReference && '0' !== $documentReference ? new DocumentReference($documentReference) : null;
    }

    public static function create(
        Status $status,
        ?string $documentReference = null,
        ?DocumentStatus $documentStatus = null,
        ?InvoiceStatus $invoiceStatus = null,
    ): self {
        return new self(
            $status,
            $documentReference,
            $documentStatus,
            $invoiceStatus,
        );
    }
}
