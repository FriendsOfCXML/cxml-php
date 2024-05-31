<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['from', 'to', 'sender'])]
readonly class Header
{
    public function __construct(
        #[Serializer\SerializedName('From')]
        private Party $from,
        #[Serializer\SerializedName('To')]
        private Party $to,
        #[Serializer\SerializedName('Sender')]
        private Party $sender,
    ) {
    }

    public function getFrom(): Party
    {
        return $this->from;
    }

    public function getTo(): Party
    {
        return $this->to;
    }

    public function getSender(): Party
    {
        return $this->sender;
    }
}
