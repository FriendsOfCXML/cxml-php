<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Response
{
    /**
     * @Ser\SerializedName("Status")
     */
    private ?Status $status;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("Id")
     */
    private ?string $id;

    /**
     * @Ser\Exclude
     * see JmsEventSubscriber
     */
    private ?ResponseInterface $payload;

    public function __construct(
    	?ResponseInterface $payload,
		?Status $status = null,
		?string $id = null)
    {
        $this->status = $status;
        $this->id = $id;
        $this->payload = $payload;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPayload(): ?ResponseInterface
    {
        return $this->payload;
    }
}
