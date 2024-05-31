<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\CXml;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['buyerCookie', 'extrinsics', 'browserFormPost', 'supplierSetup', 'shipTo', 'selectedItem', 'itemOut'])]
class Request
{
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('deploymentMode')]
    private ?string $deploymentMode;

    public function __construct(
        #[Serializer\Exclude]
        private readonly RequestPayloadInterface $payload,
        #[Serializer\SerializedName('Status')]
        private readonly ?Status $status = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        private readonly ?string $id = null,
        string $deploymentMode = null
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
