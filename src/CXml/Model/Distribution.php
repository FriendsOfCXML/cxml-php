<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Distribution
{
    private function __construct(
        #[Serializer\SerializedName('Accounting')]
        private Accounting $accounting,
    ) {
    }
}
