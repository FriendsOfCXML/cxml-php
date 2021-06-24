<?php

namespace Mathielen\CXml\Handler;

use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\PayloadInterface;

interface HandlerInterface
{
    public function handle(PayloadInterface $payload, ?Header $header = null): ?CXml;
}
