<?php

namespace CXml\Handler\Request;

use CXml\Context;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistry;
use CXml\Model\PayloadInterface;
use CXml\Model\Response\ProfileResponse;
use CXml\Model\ResponseInterface;
use CXml\Model\Transaction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SelfAwareProfileRequestHandler implements HandlerInterface
{
	private HandlerRegistry $handlerRegistry;
	private UrlGeneratorInterface $urlGenerator;
	private string $defaultRoute;

	public function __construct(HandlerRegistry $handlerRegistry, UrlGeneratorInterface $urlGenerator, string $defaultRoute = 'post_cxml')
	{
		$this->handlerRegistry = $handlerRegistry;
		$this->urlGenerator = $urlGenerator;
		$this->defaultRoute = $defaultRoute;
	}

	public function handle(PayloadInterface $payload, Context $context): ?ResponseInterface
	{
		$profileResponse = new ProfileResponse();

		foreach ($this->handlerRegistry->all() as $requestName => $handler) {
			$transaction = new Transaction($requestName, $this->getEndpointUrl());

			$profileResponse->addTransaction($transaction);
		}

		return $profileResponse;
	}

	private function getEndpointUrl(): string
	{
		return $this->urlGenerator->generate($this->defaultRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
	}

	public static function getRequestName(): string
	{
		return 'ProfileRequest';
	}
}
