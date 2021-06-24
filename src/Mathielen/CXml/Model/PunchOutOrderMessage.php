<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class PunchOutOrderMessage implements MessageInterface
{
    /**
     * @Ser\SerializedName("BuyerCookie")
     */
    private string $buyerCookie = '34234234ADFSDF234234';

    //TODO
}
