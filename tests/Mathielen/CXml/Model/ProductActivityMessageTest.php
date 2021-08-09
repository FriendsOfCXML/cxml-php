<?php

namespace Mathielen\CXml\Model;

use Mathielen\CXml\Builder;
use Mathielen\CXml\Endpoint;
use Mathielen\CXml\Model\Message\ProductActivityDetail;
use Mathielen\CXml\Model\Message\ProductActivityMessage;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;
use PHPUnit\Framework\TestCase;

class ProductActivityMessageTest extends TestCase implements PayloadIdentityFactoryInterface
{
	public function testMinimumExample(): void
	{
		$from = new Credential(
			'NetworkId',
			'AN00000123'
		);
		$to = new Credential(
			'NetworkId',
			'AN00000456'
		);
		$sender = new Credential(
			'NetworkId',
			'AN00000123',
			'abracadabra'
		);

		$statusUpdateRequest = ProductActivityMessage::create(
			'CP12465192-1552965424130',
			'SMI',
			new \DateTime('2019-02-20T14:39:48-08:00')
		)->addProductActivityDetail(
			ProductActivityDetail::create(
				new ItemId('SII99825', null, 'II99825'),
				new Inventory(new StockQuantity(200, 'EA')),
				new MultilanguageString(null, 'Assembly Line', 'EN')
			)
		);

		$cxml = Builder::create('en-US', $this)
			->from($from)
			->to($to)
			->sender($sender, 'Supplier’s Super Order Processor')
			->payload($statusUpdateRequest)
			->build();

		$xml = Endpoint::serialize($cxml);

		$this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/ProductActivityMessage.xml', $xml);
	}

	public function newPayloadIdentity(): PayloadIdentity
	{
		return new PayloadIdentity(
			'0c30050@supplierorg.com',
			new \DateTime('2021-01-08T23:00:06-08:00')
		);
	}
}
