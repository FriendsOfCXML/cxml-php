<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Message implements PayloadInterface
{
    /**
     * @Ser\SerializedName("Status")
     */
    private ?Status $status;

    /**
     * @Ser\XmlAttribute
     */
    private ?string $deploymentMode;

    /**
     * @Ser\XmlAttribute
     */
    private ?string $inReplyTo;

    /**
     * @Ser\XmlAttribute
     */
    private ?string $id;

    /**
     * @Ser\Exclude()
     * see JmsEventSubscriber
     */
    private MessageInterface $message;

    public function __construct(
        MessageInterface $message,
        Status $status = null,
        string $id = null,
        string $deploymentMode = null,
        string $inReplyTo = null
    )
    {
        $this->status = $status;
        $this->message = $message;
        $this->deploymentMode = $deploymentMode;
        $this->inReplyTo = $inReplyTo;
        $this->id = $id;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getDeploymentMode(): ?string
    {
        return $this->deploymentMode;
    }

    public function getInReplyTo(): ?string
    {
        return $this->inReplyTo;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }
}
