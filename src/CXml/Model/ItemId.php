<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['supplierPartId', 'supplierPartAuxiliaryId', 'buyerPartId', 'idReferences'])]
class ItemId
{
    use IdReferencesTrait;

    public function __construct(
        #[Serializer\SerializedName('SupplierPartID')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly string $supplierPartId,
        #[Serializer\SerializedName('SupplierPartAuxiliaryID')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly ?string $supplierPartAuxiliaryId = null,
        #[Serializer\SerializedName('BuyerPartID')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly ?string $buyerPartId = null,
    ) {
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
