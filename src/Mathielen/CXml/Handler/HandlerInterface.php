<?php

namespace Mathielen\CXml\Handler;

use Mathielen\CXml\Context;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\ResponseInterface;

interface HandlerInterface
{
	public function handle(PayloadInterface $payload, Context $context): ?ResponseInterface;

	public static function getRequestName(): string;
}
