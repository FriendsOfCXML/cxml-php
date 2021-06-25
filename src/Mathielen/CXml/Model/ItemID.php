<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemID
{
    /**
     * @Ser\SerializedName("SupplierPartID")
     */
    private string $supplierPartId;

    /**
     * @Ser\SerializedName("SupplierPartAuxiliaryID")
     */
    private string $supplierPartAuxiliaryId;
}
