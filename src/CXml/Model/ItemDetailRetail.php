<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class ItemDetailRetail
{
    public function __construct(
        /**
         * @var Characteristic[]
         */
        #[Serializer\XmlList(entry: 'Characteristic', inline: true)]
        #[Serializer\Type('array<CXml\Model\Characteristic>')]
        public array $characteristics = [],
    ) {
    }
}
