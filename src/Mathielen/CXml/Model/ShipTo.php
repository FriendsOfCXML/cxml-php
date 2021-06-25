<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ShipTo
{
    /**
     * @Ser\SerializedName("Address")
     */
    private Address $address;
}
