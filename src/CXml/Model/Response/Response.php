<?php

namespace CXml\Model\Response;

use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['status'])]
readonly class Response
{
    public function __construct(
        #[Serializer\SerializedName('Status')]
        private Status $status,
        #[Serializer\Exclude]
        private ?ResponsePayloadInterface $payload = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        private ?string $id = null
    ) {
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPayload(): ?ResponsePayloadInterface
    {
        return $this->payload ?? null;
    }
}
