<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use JMS\Serializer\Annotation as Serializer;

readonly class ProductActivityHeader
{
    public const PROCESSTYPE_SUPPLIER_MANAGED_INVENTORY = 'SMI';

    public const PROCESSTYPE_THIRD_PARTY_LOGISTICS = '3PL';

    public function __construct(
        #[Serializer\SerializedName('messageID')]
        #[Serializer\XmlAttribute]
        private string $messageId,
        #[Serializer\XmlAttribute]
        private ?string $processType = null,
        #[Serializer\XmlAttribute]
        private ?\DateTimeInterface $creationDate = null,
    ) {
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getProcessType(): ?string
    {
        return $this->processType;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }
}
