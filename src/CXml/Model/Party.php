<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['credential', 'userAgent'])]
readonly class Party
{
    public function __construct(
        #[Serializer\SerializedName('Credential')]
        public Credential $credential,
        #[Serializer\SerializedName('UserAgent')]
        #[Serializer\XmlElement(cdata: false)]
        public ?string $userAgent = null,
    ) {
    }
}
