<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\CXml;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

class Message
{
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('deploymentMode')]
    private ?string $deploymentMode = null;

    public function __construct(
        #[Serializer\Exclude]
        private readonly MessagePayloadInterface $payload,
        #[Serializer\SerializedName('Status')]
        private readonly ?Status $status = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        private readonly ?string $id = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('inReplyTo')]
        private readonly ?string $inReplyTo = null,
        string $deploymentMode = null,
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [CXml::DEPLOYMENT_PROD, CXml::DEPLOYMENT_TEST]);
        }

        $this->deploymentMode = $deploymentMode;
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
