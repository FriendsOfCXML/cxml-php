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
        public MessagePayloadInterface $payload,
        #[Serializer\SerializedName('Status')]
        public ?Status $status = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        public ?string $id = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('inReplyTo')]
        public ?string $inReplyTo = null,
        #[Serializer\SerializedName('deploymentMode')]
        #[Serializer\XmlAttribute]
        public ?string $deploymentMode = null,
    ) {
        if (null !== $deploymentMode) {
            Assertion::inArray($deploymentMode, [CXml::DEPLOYMENT_PROD, CXml::DEPLOYMENT_TEST]);
        }
    }
}
