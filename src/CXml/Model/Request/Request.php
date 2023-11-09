<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Ser;

class Request
{
    public const DEPLOYMENT_TEST = 'test';
    public const DEPLOYMENT_PROD = 'production';

    /**
     * @Ser\SerializedName("Status")
     */
    private ?Status $status = null;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("deploymentMode")
     */
    private ?string $deploymentMode = null;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("Id")
     */
    private ?string $id = null;

    /**
     * @Ser\Exclude
     * see CXmlWrappingNodeJmsEventSubscriber
     */
    private RequestPayloadInterface $payload;

    public function __construct(
        RequestPayloadInterface $payload,
        Status $status = null,
        string $id = null,
        string $deploymentMode = null
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [self::DEPLOYMENT_PROD, self::DEPLOYMENT_TEST]);
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
