<?php

namespace Mathielen\CXml\Model\Request;

use Mathielen\CXml\Model\DocumentReference;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\RequestInterface;
use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Status;

class StatusUpdateRequest implements RequestInterface
{
    /**
     * @Ser\SerializedName("DocumentReference")
     */
    private ?DocumentReference $documentReference;

    /**
     * @Ser\SerializedName("Status")
     */
    private Status $status;

    /**
     * @Ser\XmlList(inline=true, entry="Extrinsic")
     * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
     *
     * @var Extrinsic[]
     */
    private array $extrinsics = [];

    public function __construct(Status $status, ?string $documentReference = null)
    {
        $this->status = $status;
        $this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
    }

    public function addExtrinsic(Extrinsic $extrinsic): self
    {
        $this->extrinsics[] = $extrinsic;

        return $this;
    }

    public function getDocumentReference(): ?DocumentReference
    {
        return $this->documentReference;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getExtrinsics(): array
    {
        return $this->extrinsics;
    }
}
