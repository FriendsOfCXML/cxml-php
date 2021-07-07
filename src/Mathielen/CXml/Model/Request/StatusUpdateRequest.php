<?php

namespace Mathielen\CXml\Model\Request;

use Mathielen\CXml\Model\DocumentReference;
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

    public function __construct(Status $status, ?string $documentReference = null)
    {
        $this->status = $status;
        $this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
    }
}
