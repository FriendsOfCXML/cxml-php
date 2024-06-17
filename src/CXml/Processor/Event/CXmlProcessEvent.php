<?php

namespace CXml\Processor\Event;

use CXml\Context;
use CXml\Model\CXml;

class CXmlProcessEvent
{
    private CXml $cxml;
    private Context $context;

    public function __construct(CXml $cxml, Context $context)
    {
        $this->cxml = $cxml;
        $this->context = $context;
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
