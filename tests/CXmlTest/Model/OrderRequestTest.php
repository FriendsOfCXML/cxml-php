<?php

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\Classification;
use CXml\Model\Comment;
use CXml\Model\Country;
use CXml\Model\Credential;
use CXml\Model\Description;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemOut;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\PayloadIdentity;
use CXml\Model\PostalAddress;
use CXml\Model\Request\OrderRequest;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Model\Request\Request;
use CXml\Model\ShipTo;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class OrderRequestTest extends TestCase implements PayloadIdentityFactoryInterface
{
	public function testMinimumExample(): void
	{
		$from = new Credential(
			'NetworkId',
			'inbound@prominate-platform.com'
		);
		$to = new Credential(
			'NetworkId',
			'supplier@supplier.com'
		);
		$sender = new Credential(
			'NetworkId',
			'inbound@prominate-platform.com',
			'coyote'
		);

		$orderRequestHeader = OrderRequestHeader::create(
			'DO1234',
			new \DateTime('2000-10-12T18:41:29-08:00'),
			new ShipTo(
				new Address(
					new MultilanguageString('Acme'),
					new PostalAddress(
						[
							'Joe Smith',
							'Mailstop M-543',
						],
						[
							'123 Anystreet',
						],
						'Sunnyvale',
						new Country('US', 'United States'),
						null,
						'CA',
						'90489',
						'default'
					)
				)
			),
			new BillTo(
				new Address(
					new MultilanguageString('Zinc GmbH'),
					new PostalAddress(
						[],
						[
							'An den Eichen 18',
						],
						'Solingen',
						new Country('DE', 'Deutschland'),
						null,
						null,
						'42699',
						'default'
					)
				)
			),
			new MoneyWrapper(
				'EUR',
				8500
			),
			[new Comment(null, null, null, 'delivery-note.pdf')]
		);

		$orderRequest = OrderRequest::create(
			$orderRequestHeader
		);

		$item = ItemOut::create(
			1,
			10,
			new ItemId('1233244'),
			ItemDetail::create(
				new Description('hello from item 1'),
				'EA',
				new MoneyWrapper(
					'EUR',
					210
				),
				[
					new Classification('custom', 0),
				]
			),
			new \DateTime('2020-02-28')
		);
		$orderRequest->addItem($item);

		$item = ItemOut::create(
			2,
			20,
			new ItemId('1233245'),
			ItemDetail::create(
				new Description('hello from item 2'),
				'EA',
				new MoneyWrapper(
					'EUR',
					320
				),
				[
					new Classification('custom', 0),
				]
			),
			new \DateTime('2020-02-28')
		);
		$orderRequest->addItem($item);

		$cxml = Builder::create('Platform Order Fulfillment Hub', null, $this)
			->from($from)
			->to($to)
			->sender($sender)
			->payload($orderRequest)
			->build(Request::DEPLOYMENT_TEST)
		;

		$this->assertEquals('OrderRequest_1625586002.193314.7293@dev', (string) $cxml);

		$xml = Serializer::create()->serialize($cxml);
		$this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/OrderRequest.xml', $xml);
	}

	public function newPayloadIdentity(): PayloadIdentity
	{
		return new PayloadIdentity(
			'1625586002.193314.7293@dev',
			new \DateTime('2000-10-12T18:39:09-08:00')
		);
	}
}
