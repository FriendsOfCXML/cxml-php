<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['from', 'to', 'sender'])]
readonly class Header
{
    public function __construct(
        #[Serializer\SerializedName('From')]
        public Party $from,
        #[Serializer\SerializedName('To')]
        public Party $to,
        #[Serializer\SerializedName('Sender')]
        public Party $sender,
    ) {
    }
}
