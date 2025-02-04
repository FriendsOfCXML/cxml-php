<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\DocumentReference;
use CXml\Model\Status;
use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['documentReference', 'extrinsics'])]
class StatusUpdateRequest implements RequestPayloadInterface
{
    use ExtrinsicsTrait;

    #[Serializer\SerializedName('DocumentReference')]
    public readonly ?DocumentReference $documentReference;

    public function __construct(
        #[Serializer\SerializedName('Status')]
        public readonly Status $status,
        ?string $documentReference = null,
    ) {
        $this->documentReference = null !== $documentReference && '' !== $documentReference && '0' !== $documentReference ? new DocumentReference($documentReference) : null;
    }

    public static function create(Status $status, ?string $documentReference = null): self
    {
        return new self(
            $status,
            $documentReference,
        );
    }
}
