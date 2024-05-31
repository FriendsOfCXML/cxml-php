<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class DocumentReference
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('payloadID')]
        private string $payloadId
    ) {
    }

    public function getPayloadId(): string
    {
        return $this->payloadId;
    }
}
