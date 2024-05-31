<?php

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\CXml;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

class Message
{
    #[Serializer\SerializedName('Status')]
    private ?Status $status = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('deploymentMode')]
    private ?string $deploymentMode = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('inReplyTo')]
    private ?string $inReplyTo = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('Id')]
    private ?string $id = null;

    #[Serializer\Exclude] // see CXmlWrappingNodeJmsEventSubscriber
    private MessagePayloadInterface $payload;

    public function __construct(
        MessagePayloadInterface $message,
        Status $status = null,
        string $id = null,
        string $inReplyTo = null,
        string $deploymentMode = null
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [CXml::DEPLOYMENT_PROD, CXml::DEPLOYMENT_TEST]);
        }

        $this->status = $status;
        $this->payload = $message;
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

    public function getPayload(): MessagePayloadInterface
    {
        return $this->payload;
    }
}
