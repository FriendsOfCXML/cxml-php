<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\CXml;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['buyerCookie', 'extrinsics', 'browserFormPost', 'supplierSetup', 'shipTo', 'selectedItem', 'itemOut'])]
readonly class Request
{
    public function __construct(
        #[Serializer\Exclude]
        public RequestPayloadInterface $payload,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        public ?string $id = null,
        #[Serializer\SerializedName('deploymentMode')]
        #[Serializer\XmlAttribute]
        public ?string $deploymentMode = null,
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [CXml::DEPLOYMENT_PROD, CXml::DEPLOYMENT_TEST]);
        }
    }
}
