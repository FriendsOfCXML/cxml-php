<?php

namespace Mathielen\CXml\Model;

use Mathielen\CXml\Builder;
use Mathielen\CXml\Endpoint;
use Mathielen\CXml\Model\Message\PunchOutOrderMessage;
use Mathielen\CXml\Model\Message\PunchOutOrderMessageHeader;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;
use Mathielen\CXml\Validation\DtdValidator;
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
