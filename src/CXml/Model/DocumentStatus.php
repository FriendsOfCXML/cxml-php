<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\CommentsTrait;
use JMS\Serializer\Annotation as Serializer;

/**
 * Generic status of a referenced document, used inside a StatusUpdateRequest.
 *
 * DTD: <!ELEMENT DocumentStatus (DocumentInfo?, ItemStatus*, Comments*)>
 * The possible values of $type depend on the network hub and the type of document being updated.
 */
#[Serializer\AccessorOrder(order: 'custom', custom: ['type', 'documentInfo', 'comments'])]
class DocumentStatus
{
    use CommentsTrait;

    public function __construct(
        #[Serializer\XmlAttribute]
        public readonly string $type,
        #[Serializer\SerializedName('DocumentInfo')]
        public readonly ?DocumentInfo $documentInfo = null,
    ) {
    }

    public static function create(string $type, ?DocumentInfo $documentInfo = null): self
    {
        return new self($type, $documentInfo);
    }
}
