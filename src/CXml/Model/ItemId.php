<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ItemId
{
    use IdReferencesTrait;

    #[Serializer\SerializedName('SupplierPartID')]
    #[Serializer\XmlElement(cdata: false)]
    private string $supplierPartId;

    #[Serializer\SerializedName('SupplierPartAuxiliaryID')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $supplierPartAuxiliaryId = null;

    #[Serializer\SerializedName('BuyerPartID')]
    #[Serializer\XmlElement(cdata: false)]
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
