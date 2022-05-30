<?php

namespace CXml\Processor;

use CXml\Builder;
use CXml\Context;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CxmlConflictException;
use CXml\Exception\CXmlException;
use CXml\Exception\CXmlExpectationFailedException;
use CXml\Exception\CXmlNotAcceptableException;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Exception\CXmlPreconditionFailedException;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistryInterface;
use CXml\Model;
use CXml\Model\CXml;
use CXml\Model\PayloadInterface;
use CXml\Model\ResponseInterface;
use CXml\Model\Status;
use CXml\Processor\Exception\CXmlProcessException;
use Symfony\Component\HttpFoundation\Response;

class Processor
{
	// according to cXML reference document
	private static array $exceptionMapping = [
		CXmlAuthenticationInvalidException::class => 401,
		CXmlNotAcceptableException::class => 406,
		CxmlConflictException::class => 409,
		CXmlPreconditionFailedException::class => 412,
		CXmlExpectationFailedException::class => 417,
		CXmlNotImplementedException::class => 450,
	];

	private static array $exceptionCodeMapping = [
		450 => 'Not Implemented',
	];

	private HeaderProcessor $headerProcessor;
	private HandlerRegistryInterface $handlerRegistry;
	private Builder $builder;

	public function __construct(
		HeaderProcessor $requestProcessor,
		HandlerRegistryInterface $handlerRepository,
		Builder $builder
	) {
		$this->headerProcessor = $requestProcessor;
		$this->handlerRegistry = $handlerRepository;
		$this->builder = $builder;
	}

	/**
	 * @throws CXmlException
	 */
	public function process(CXml $cxml, Context $context = null): ?CXml
	{
		$context ??= Context::create();
		$context->setCXml($cxml);

		$request = $cxml->getRequest();
		if ($request) {
			return $this->processRequest($request, $context);
		}

		$response = $cxml->getResponse();
		if ($response) {
			$this->processResponse($response, $context);

			return null;
		}

		$message = $cxml->getMessage();
		if ($message) {
			$this->processMessage($message, $context);

			return null;
		}

		throw new CXmlException('Invalid CXml. Either request, response or message must be given.');
	}

	private function getHandlerForPayload(PayloadInterface $payload): HandlerInterface
	{
		$handlerId = (new \ReflectionClass($payload))->getShortName();

		return $this->handlerRegistry->get($handlerId);
	}

	/**
	 * @throws CXmlProcessException
	 * @throws CXmlException
	 */
	private function processMessage(Model\Message $message, Context $context): void
	{
		$payload = $message->getPayload();

		try {
			$this->getHandlerForPayload($payload)->handle($payload, $context);
		} catch (\Exception $e) {
			throw new CXmlProcessException($e);
		}
	}

	/**
	 * @throws CXmlProcessException
	 * @throws CXmlException
	 */
	private function processResponse(Model\Response $response, Context $context): void
	{
		$payload = $response->getPayload();

		if (!$payload instanceof ResponseInterface) {
			return;
		}

		try {
			$this->getHandlerForPayload($payload)->handle($payload, $context);
		} catch (\Exception $e) {
			throw new CXmlProcessException($e);
		}
	}

	/**
	 * @throws CXmlProcessException
	 * @throws CXmlException
	 */
	private function processRequest(Model\Request $request, Context $context): CXml
	{
		$header = $context->getCXml() ? $context->getCXml()->getHeader() : null;
		if (!$header) {
			throw new CXmlException('Invalid CXml. Header is mandatory for request message.');
		}

		try {
			$this->headerProcessor->process($header);
		} catch (\Exception $e) {
			throw new CXmlProcessException($e);
		}

		$payload = $request->getPayload();
		$handler = $this->getHandlerForPayload($payload);

		$response = $handler->handle($payload, $context);

		// if no response was returned, set an implicit 200/OK
		if (!$response) {
			$this->builder->status(new Model\Status(
				200,
				'OK'
			));
		}

		return $this->builder
			->payload($response)
			->build()
		;
	}

	public function processException(CXmlException $exception): CXml
	{
		$statusCode = self::$exceptionMapping[\get_class($exception)] ?? 500;
		$statusText = self::$exceptionCodeMapping[$statusCode] ?? Response::$statusTexts[$statusCode];
		$status = new Status($statusCode, $statusText, $exception->getMessage());

		return $this->builder
			->status($status)
			->build()
		;
	}
}
