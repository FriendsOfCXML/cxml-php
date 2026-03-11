<?php

declare(strict_types=1);

namespace CXml\Model;

use Assert\Assertion;
use CXml\Model\Trait\CommentsTrait;
use JMS\Serializer\Annotation as Serializer;
use ReflectionClass;

#[Serializer\XmlRoot('Index')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['supplierId', 'comments', 'indexItems'])]
class Index
{
    use CommentsTrait;

    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('loadmode')]
        public readonly string $loadmode,
        #[Serializer\SerializedName('SupplierID')]
        public readonly SupplierId $supplierId,
        /**
         * @var IndexItemWrapper[]
         */
        #[Serializer\XmlList(entry: 'IndexItem', inline: true)]
        #[Serializer\Type('array<CXml\Model\IndexItemWrapper>')]
        private array $indexItems = [],
        #[Serializer\Exclude]
        public string $dtdUri = 'http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd',
    ) {
        Assertion::inArray($this->loadmode, ['Full', 'Incremental']);
    }

    public static function create(string $loadmode, SupplierId $supplierId): self
    {
        return new self($loadmode, $supplierId);
    }

    public function addIndexItem(IndexItemInterface $indexItem): self
    {
        $shortClsName = (new ReflectionClass($indexItem))->getShortName();

        if (!isset($this->indexItems[$shortClsName])) {
            $this->indexItems[$shortClsName] = new IndexItemWrapper();
        }

        $this->indexItems[$shortClsName]->addIndexItem($indexItem);

        return $this;
    }
}
