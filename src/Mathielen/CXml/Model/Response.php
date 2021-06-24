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
     */
    private ?string $id;

    /**
     * @Ser\Exclude()
     * see JmsEventSubscriber
     */
    private ?ResponseInterface $response;

    public function __construct(?ResponseInterface $response, ?Status $status = null, ?string $id = null)
    {
        $this->status = $status;
        $this->id = $id;
        $this->response = $response;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
