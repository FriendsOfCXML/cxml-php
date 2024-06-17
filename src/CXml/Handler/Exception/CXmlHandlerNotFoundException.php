<?php

declare(strict_types=1);

namespace CXml\Handler\Exception;

use CXml\Exception\CXmlNotImplementedException;
use Throwable;

use function sprintf;

class CXmlHandlerNotFoundException extends CXmlNotImplementedException
{
    public function __construct(string $handlerId, Throwable $previous = null)
    {
        parent::__construct(sprintf('Handler for %s not found. Register first.', $handlerId), $previous);
    }
}
