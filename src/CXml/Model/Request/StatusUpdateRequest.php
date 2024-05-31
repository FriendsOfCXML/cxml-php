<?php

namespace CXml\Model\Request;

use CXml\Model\DocumentReference;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

class StatusUpdateRequest implements RequestPayloadInterface
{
    use ExtrinsicsTrait;

    #[Serializer\SerializedName('DocumentReference')]
    private ?DocumentReference $documentReference = null;

    #[Serializer\SerializedName('Status')]
    private Status $status;

    public function __construct(Status $status, string $documentReference = null)
    {
        $this->status = $status;
        $this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
    }

    public static function create(Status $status, string $documentReference = null): self
    {
        return new self(
            $status,
            $documentReference
        );
    }

    public function getDocumentReference(): ?DocumentReference
    {
        return $this->documentReference;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
