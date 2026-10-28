<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InvoiceDetailHeaderIndicator
{
    // the dtd only allows "yes" or omitting the attribute
    #[Serializer\XmlAttribute]
    public ?string $isHeaderInvoice;

    public function __construct(bool $isHeaderInvoice = false)
    {
        $this->isHeaderInvoice = $isHeaderInvoice ? 'yes' : null;
    }
}
