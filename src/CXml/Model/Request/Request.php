<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\CXml;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

class Request
{
    #[Serializer\SerializedName('Status')]
    private ?Status $status = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('deploymentMode')]
    private ?string $deploymentMode = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('Id')]
    private ?string $id = null;

    #[Serializer\Exclude] // see CXmlWrappingNodeJmsEventSubscriber
    private RequestPayloadInterface $payload;

    public function __construct(
        RequestPayloadInterface $payload,
        Status $status = null,
        string $id = null,
        string $deploymentMode = null
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [CXml::DEPLOYMENT_PROD, CXml::DEPLOYMENT_TEST]);
        }

        $this->status = $status;
        $this->id = $id;
        $this->payload = $payload;
        $this->deploymentMode = $deploymentMode;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDeploymentMode(): ?string
    {
        return $this->deploymentMode;
    }

    public function getPayload(): RequestPayloadInterface
    {
        return $this->payload;
    }
}
