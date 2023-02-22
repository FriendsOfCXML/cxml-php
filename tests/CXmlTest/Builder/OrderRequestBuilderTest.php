<?php

namespace CXmlTest\Builder;

use CXml\Builder;
use CXml\Builder\OrderRequestBuilder;
use CXml\Model\Credential;
use CXml\Model\PayloadIdentity;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class OrderRequestBuilderTest extends TestCase implements PayloadIdentityFactoryInterface
{
	public function testFromPunchOutOrderMessage(): void
	{
		$serializer = Serializer::create();
		$poomXml = \file_get_contents(__DIR__.'/fixtures/poom.xml');
		$poom = $serializer->deserialize($poomXml);

		$orb = OrderRequestBuilder::fromPunchOutOrderMessage($poom->getMessage()->getPayload());
		$actualOrderRequest = $orb
			->billTo('name')
			->build()
		;

		$actualOrderRequest = Builder::create('cxml-php UserAgent', null, $this)
			->payload($actualOrderRequest)
			->from(new Credential('NetworkId', 'inbound@prominate-platform.com'))
			->to(new Credential('NetworkId', 'supplier@supplier.com'))
			->sender(new Credential('NetworkId', 'inbound@prominate-platform.com'))
			->build()
		;
		$actualOrderRequest = $serializer->serialize($actualOrderRequest);

		$expectedOrderRequest = \file_get_contents(__DIR__.'/fixtures/order_request.xml');

		$this->assertXmlStringEqualsXmlString($expectedOrderRequest, $actualOrderRequest);
	}

	public function newPayloadIdentity(): PayloadIdentity
	{
		return new PayloadIdentity(
			'933695160894',
			new \DateTime('2021-01-08T23:00:06-08:00')
		);
	}
}
