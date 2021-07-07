<?php

namespace Mathielen\CXml\Handler;

use Mathielen\CXml\Model\CXml;

class Context
{
    private CXml $cxml;

    public function __construct(CXml $cxml)
    {
        $this->cxml = $cxml;
    }

    public function getCxml(): CXml
    {
        return $this->cxml;
    }
}
