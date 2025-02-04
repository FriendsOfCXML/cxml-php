<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['supplierPartId', 'supplierPartAuxiliaryId', 'buyerPartId', 'idReferences'])]
class ItemId
{
    use IdReferencesTrait;

    public function __construct(
        #[Serializer\SerializedName('SupplierPartID')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly string $supplierPartId,
        #[Serializer\SerializedName('SupplierPartAuxiliaryID')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?string $supplierPartAuxiliaryId = null,
        #[Serializer\SerializedName('BuyerPartID')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?string $buyerPartId = null,
    ) {
    }
}
