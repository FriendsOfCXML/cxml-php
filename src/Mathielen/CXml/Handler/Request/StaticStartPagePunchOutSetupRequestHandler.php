<?php

namespace Mathielen\CXml\Handler\Request;

use Mathielen\CXml\Context;
use Mathielen\CXml\Handler\HandlerInterface;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\Response\PunchoutSetupResponse;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Model\Url;

class StaticStartPagePunchOutSetupRequestHandler implements HandlerInterface
{
	private string $startPageUrl;

	public function __construct(string $startPageUrl)
	{
		$this->startPageUrl = $startPageUrl;
	}

	public function handle(PayloadInterface $payload, Context $context): ?ResponseInterface
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
