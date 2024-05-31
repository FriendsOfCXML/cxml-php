<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemId
{
    use IdReferencesTrait;

    #[Ser\SerializedName('SupplierPartID')]
    #[Ser\XmlElement(cdata: false)]
    private string $supplierPartId;

    #[Ser\SerializedName('SupplierPartAuxiliaryID')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $supplierPartAuxiliaryId = null;

    #[Ser\SerializedName('BuyerPartID')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $buyerPartId = null;

    public function __construct(string $supplierPartId, string $supplierPartAuxiliaryId = null, string $buyerPartId = null)
    {
        $this->supplierPartId = $supplierPartId;
        $this->supplierPartAuxiliaryId = $supplierPartAuxiliaryId;
        $this->buyerPartId = $buyerPartId;
    }

    public function getSupplierPartId(): string
    {
        return $this->supplierPartId;
    }

    public function getSupplierPartAuxiliaryId(): ?string
    {
        return $this->supplierPartAuxiliaryId;
    }

    public function getBuyerPartId(): ?string
    {
        return $this->buyerPartId;
    }
}
