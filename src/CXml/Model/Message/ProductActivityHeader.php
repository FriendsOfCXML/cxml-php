<?php

namespace CXml\Model\Message;

use JMS\Serializer\Annotation as Serializer;

class ProductActivityHeader
{
    public const PROCESSTYPE_SUPPLIER_MANAGED_INVENTORY = 'SMI';
    public const PROCESSTYPE_THIRD_PARTY_LOGISTICS = '3PL';

    #[Serializer\SerializedName('messageID')]
    #[Serializer\XmlAttribute]
    private string $messageId;

    #[Serializer\XmlAttribute]
    private ?string $processType = null;

    #[Serializer\XmlAttribute]
    private ?\DateTimeInterface $creationDate = null;

    public function __construct(string $messageId, string $processType = null, \DateTimeInterface $creationDate = null)
    {
        $this->messageId = $messageId;
        $this->processType = $processType;
        $this->creationDate = $creationDate;
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
