<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class DocumentReference
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('payloadID')]
        public string $payloadId,
    ) {
    }
}
