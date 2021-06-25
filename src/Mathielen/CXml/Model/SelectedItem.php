<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class SelectedItem
{
    /**
     * @Ser\SerializedName("ItemID")
     */
    private ItemID $itemId;
}
