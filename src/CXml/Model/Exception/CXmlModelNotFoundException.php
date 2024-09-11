<?php

declare(strict_types=1);

namespace CXml\Model\Exception;

use CXml\Exception\CXmlNotImplementedException;

class CXmlModelNotFoundException extends CXmlNotImplementedException
{
    public function __construct(string $xmlNodeName)
    {
        parent::__construct('Model not found for cXML-node: ' . $xmlNodeName);
    }
}
