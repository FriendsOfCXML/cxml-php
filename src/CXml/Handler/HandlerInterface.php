<?php

namespace CXml\Handler;

use CXml\Context;
use CXml\Model\PayloadInterface;
use CXml\Model\ResponseInterface;

interface HandlerInterface
{
	public function handle(PayloadInterface $payload, Context $context): ?ResponseInterface;

	public static function getRequestName(): string;
}
