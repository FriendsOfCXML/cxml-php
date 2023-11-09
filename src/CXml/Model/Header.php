<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Header
{
    /**
     * @Ser\SerializedName("From")
     */
    private Party $from;

    /**
     * @Ser\SerializedName("To")
     */
    private Party $to;

    /**
     * @Ser\SerializedName("Sender")
     */
    private Party $sender;

    public function __construct(Party $from, Party $to, Party $sender)
    {
        $this->from = $from;
        $this->to = $to;
        $this->sender = $sender;
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
