<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemId
{
    /**
     * @Ser\SerializedName("SupplierPartID")
     * @Ser\XmlElement (cdata=false)
     */
    private string $supplierPartId;

    /**
     * @Ser\SerializedName("SupplierPartAuxiliaryID")
     * @Ser\XmlElement (cdata=false)
     */
    private ?string $supplierPartAuxiliaryId;

    public function __construct(string $supplierPartId, string $supplierPartAuxiliaryId = null)
    {
        $this->supplierPartId = $supplierPartId;
        $this->supplierPartAuxiliaryId = $supplierPartAuxiliaryId;
    }
}
