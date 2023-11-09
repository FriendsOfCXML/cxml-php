<?php

namespace CXml\Handler;

use CXml\Context;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Model\PayloadInterface;
use CXml\Model\Response\ResponsePayloadInterface;

interface HandlerInterface
{
    /**
     * @throws CXmlNotImplementedException
     */
    public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface;

    public static function getRequestName(): string;
}
