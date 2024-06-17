<?php

declare(strict_types=1);

namespace CXml\Processor\Event;

use CXml\Context;
use CXml\Model\CXml;

readonly class CXmlProcessEvent
{
    public function __construct(private CXml $cxml, private Context $context)
    {
    }

    public function getCxml(): CXml
    {
        return $this->cxml;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
