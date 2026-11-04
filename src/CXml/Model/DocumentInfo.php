<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

readonly class DocumentInfo
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('documentID')]
        public string $documentId,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('documentType')]
        public string $documentType,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('documentDate')]
        public ?DateTimeInterface $documentDate = null,
    ) {
    }
}
