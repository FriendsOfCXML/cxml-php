<?php

namespace Mathielen\CXml\Model\Message;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\MessageInterface;

class PunchOutOrderMessage implements MessageInterface
{
    /**
     * @Ser\SerializedName("BuyerCookie")
     */
    private string $buyerCookie = '34234234ADFSDF234234';

    //TODO
}
