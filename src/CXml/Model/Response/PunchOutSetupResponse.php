<?php

declare(strict_types=1);

namespace CXml\Model\Response;

use CXml\Model\Url;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['startPage'])]
readonly class PunchOutSetupResponse implements ResponsePayloadInterface
{
    public function __construct(
        #[Serializer\SerializedName('StartPage')]
        public Url $startPage,
    ) {
    }
}
