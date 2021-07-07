<?php

namespace Mathielen\CXml\Handler\Request;

use Mathielen\CXml\Handler\HandlerInterface;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\ResponseInterface;

class StatusUpdateRequestHandler implements HandlerInterface
{
    public function handle(PayloadInterface $payload, ?Header $header = null): ?ResponseInterface
    {
        // TODO: Implement handle() method.
    }

    public static function getRequestName(): string
    {
        // TODO: Implement getRequestName() method.
    }
}
