<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

readonly class ProductActivityHeader
{
    final public const PROCESSTYPE_SUPPLIER_MANAGED_INVENTORY = 'SMI';

    final public const PROCESSTYPE_THIRD_PARTY_LOGISTICS = '3PL';

    public function __construct(
        #[Serializer\SerializedName('messageID')]
        #[Serializer\XmlAttribute]
        public string $messageId,
        #[Serializer\XmlAttribute]
        public ?string $processType = null,
        #[Serializer\XmlAttribute]
        public ?DateTimeInterface $creationDate = null,
    ) {
    }
}
