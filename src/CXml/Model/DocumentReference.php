<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class DocumentReference
{
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('payloadID')]
    private string $payloadId;

    public function __construct(string $payloadId)
    {
        $this->payloadId = $payloadId;
    }

    public function getPayloadId(): string
    {
        return $this->payloadId;
    }
}
