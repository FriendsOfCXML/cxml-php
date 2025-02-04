<?php

declare(strict_types=1);

namespace CXml\Model\Response;

use CXml\Model\Status;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['status'])]
readonly class Response
{
    public function __construct(
        #[Serializer\SerializedName('Status')]
        public Status $status,
        #[Serializer\Exclude]
        public ?ResponsePayloadInterface $payload = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('Id')]
        public ?string $id = null,
    ) {
    }
}
