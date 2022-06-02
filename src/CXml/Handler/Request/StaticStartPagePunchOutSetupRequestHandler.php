<?php

namespace CXml\Handler\Request;

use CXml\Context;
use CXml\Handler\HandlerInterface;
use CXml\Model\PayloadInterface;
use CXml\Model\Response\PunchoutSetupResponse;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Url;

class StaticStartPagePunchOutSetupRequestHandler implements HandlerInterface
{
	private string $startPageUrl;

	public function __construct(string $startPageUrl)
	{
		$this->startPageUrl = $startPageUrl;
	}

	public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
	{
		return new PunchoutSetupResponse(
			new Url(
				$this->startPageUrl
			)
		);
	}

	public static function getRequestName(): string
	{
		return 'PunchOutSetupRequest';
	}
}
