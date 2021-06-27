<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemID
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
    private string $supplierPartAuxiliaryId;
}
