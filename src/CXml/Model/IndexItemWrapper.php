<?php

declare(strict_types=1);

namespace CXml\Model;

use InvalidArgumentException;
use JMS\Serializer\Annotation as Serializer;

class IndexItemWrapper
{
    public function __construct(
        #[Serializer\XmlList(entry: 'IndexItemAdd', inline: true)]
        #[Serializer\Type('array<CXml\Model\IndexItemAdd>')]
        private array $indexItemAdds = [],
        #[Serializer\XmlList(entry: 'IndexItemDelete', inline: true)]
        #[Serializer\Type('array<CXml\Model\IndexItemDelete>')]
        private array $indexItemDeletes = [],
        #[Serializer\XmlList(entry: 'IndexItemPunchout', inline: true)]
        #[Serializer\Type('array<CXml\Model\IndexItemPunchout>')]
        private array $indexItemPunchouts = [],
    ) {
    }

    public function addIndexItem(IndexItemInterface $indexItem): self
    {
        if ($indexItem instanceof IndexItemAdd) {
            $this->indexItemAdds[] = $indexItem;
        } elseif ($indexItem instanceof IndexItemDelete) {
            $this->indexItemDeletes[] = $indexItem;
        } elseif ($indexItem instanceof IndexItemPunchout) {
            $this->indexItemPunchouts[] = $indexItem;
        } else {
            throw new InvalidArgumentException('Invalid index item type');
        }

        return $this;
    }
}
