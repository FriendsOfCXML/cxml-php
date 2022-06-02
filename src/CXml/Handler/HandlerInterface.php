<?php

namespace CXml\Handler;

use CXml\Context;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Model\PayloadInterface;
use CXml\Model\ResponseInterface;

interface HandlerInterface
{

	/**
	 * @throws CXmlNotImplementedException
	 */
	public function handle(PayloadInterface $payload, Context $context): ?ResponseInterface;

	public static function getRequestName(): string;
}
