<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\CXml;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

readonly class Message
{
    public function __construct(
        #[Serializer\Exclude]
        private MessagePayloadInterface $payload,
        #[Serializer\SerializedName('Status')]
        private ?Status $status = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        private ?string $id = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('inReplyTo')]
        private ?string $inReplyTo = null,
        #[Serializer\SerializedName('deploymentMode')]
        #[Serializer\XmlAttribute]
        private ?string $deploymentMode = null,
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [CXml::DEPLOYMENT_PROD, CXml::DEPLOYMENT_TEST]);
        }
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
