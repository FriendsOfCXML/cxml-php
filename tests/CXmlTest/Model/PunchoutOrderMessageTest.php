<?php

namespace CXmlTest\Model;

use CXml\Builder;
use CXml\Endpoint;
use CXml\Model\Classification;
use CXml\Model\Credential;
use CXml\Model\ItemDetail;
use CXml\Model\ItemId;
use CXml\Model\ItemIn;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageShortName;
use CXml\Model\PayloadIdentity;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Validation\DtdValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PunchoutOrderMessageTest extends TestCase implements PayloadIdentityFactoryInterface
{
	private DtdValidator $dtdValidator;

	protected function setUp(): void
	{
		$this->dtdValidator = new DtdValidator('tests/metadata/cxml/dtd/1.2.053/');
	}

	public function testMinimumExample(): void
	{
		$from = new Credential(
			'DUNS',
			'83528721',
		);
		$to = new Credential(
			'DUNS',
			'65652314',
		);
		$sender = new Credential(
			'workchairs.com',
			'website 1',
		);

		$punchoutOrderMessage = PunchOutOrderMessage::create(
			'1CX3L4843PPZO',
			new PunchOutOrderMessageHeader(new MoneyWrapper('USD', 76320), 'create'),
		)->addPunchoutOrderMessageItem(
			ItemIn::create(
				3,
				new ItemId('5555', null, 'KD5555'),
				(new ItemDetail(
					new MultilanguageShortName('Excelsior Desk Chair', null, 'en'),
					'EA',
					new MoneyWrapper('USD', 76320)
				))->addClassification((new Classification('UNSPSC', 'ean1234')))
			)
		)->addPunchoutOrderMessageItem(
			ItemIn::create(
				5,
				new ItemId('666', null, 'KD666'),
				new ItemDetail(
					new MultilanguageShortName('22Excelsior Desk Chair', null, 'en'),
					'EA',
					new MoneyWrapper('USD', 76320)
				)
			)->addClassification('UNSPSC', 'ean1234')
		);

		$cxml = Builder::create('en-US', $this)
			->from($from)
			->to($to)
			->sender($sender, 'Workchairs cXML Application')
			->payload($punchoutOrderMessage)
			->build()
		;

		$this->assertEquals('PunchOutOrderMessage_933695160894', (string) $cxml);

		$xml = Endpoint::serialize($cxml);
		$this->dtdValidator->validateAgainstDtd($xml);

		$this->assertXmlStringEqualsXmlFile('tests/metadata/cxml/samples/PunchoutOrderMessage.xml', $xml);
	}

	public function newPayloadIdentity(): PayloadIdentity
	{
		return new PayloadIdentity(
			'933695160894',
			new \DateTime('2021-01-08T23:00:06-08:00')
		);
	}
}
