<?php

namespace Mathielen\CXml\Handler;

use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\ResponseInterface;

interface HandlerInterface
{
    public function handle(PayloadInterface $payload, ?Header $header = null): ?ResponseInterface;
    public static function getRequestName(): string;
}
