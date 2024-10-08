<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\CXml;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['buyerCookie', 'extrinsics', 'browserFormPost', 'supplierSetup', 'shipTo', 'selectedItem', 'itemOut'])]
readonly class Request
{
    public function __construct(
        #[Serializer\Exclude]
        private RequestPayloadInterface $payload,
        #[Serializer\SerializedName('Status')]
        private ?Status $status = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        private ?string $id = null,
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
