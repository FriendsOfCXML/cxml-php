<?php

namespace CXml\Model\Response;

use CXml\Model\Status;
use JMS\Serializer\Annotation as Ser;

class Response
{
    #[Ser\SerializedName('Status')]
    private Status $status;

    #[Ser\XmlAttribute]
    #[Ser\SerializedName('Id')]
    private ?string $id = null;

    #[Ser\Exclude] // see CXmlWrappingNodeJmsEventSubscriber
    private ?ResponsePayloadInterface $payload = null;

    public function __construct(
        Status $status,
        ResponsePayloadInterface $payload = null,
        string $id = null
    ) {
        $this->status = $status;
        $this->id = $id;
        $this->payload = $payload;
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
        return $this->payload;
    }
}
