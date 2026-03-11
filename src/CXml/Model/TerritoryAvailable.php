<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class TerritoryAvailable
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        public string $value,
    ) {
    }
}
