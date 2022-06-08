<?php

namespace CXmlTest\Model;

use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Header;
use CXml\Model\Message\Message;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\MoneyWrapper;
use CXml\Model\Party;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\PunchOutSetupRequest;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use CXml\Model\Status;
use CXml\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SerializerTest extends TestCase
{
	public function testSimpleRequest(): void
	{
		$from = new Party(
			new Credential('AribaNetworkUserId', 'admin@acme.com')
		);
		$to = new Party(
			new Credential('DUNS', '012345678')
		);
		$sender = new Party(
			new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
			'Network Hub 1.1'
		);
		$request = new Request(
			new PunchOutSetupRequest(
				'nomnom',
				'https://browserFormPost',
				'https://supplierSetup'
			)
		);

		$header = new Header(
			$from,
			$to,
			$sender
		);

		$msg = CXml::forRequest(
			new PayloadIdentity('payload-id', new \DateTime('2000-01-01')),
			$request,
			$header
		);

		$actualXml = Serializer::create()->serialize($msg);

		// XML copied from cXML Reference Guide
		$expectedXml =
			'<?xml version="1.0" encoding="UTF-8"?>
			<cXML payloadID="payload-id" timestamp="2000-01-01T00:00:00+00:00">
			<Header>
			<From>
			<Credential domain="AribaNetworkUserId">
			<Identity>admin@acme.com</Identity>
			</Credential>
			</From>
			<To>
			<Credential domain="DUNS">
			<Identity>012345678</Identity>
			</Credential>
			</To>
			<Sender>
			<Credential domain="AribaNetworkUserId">
			<Identity>sysadmin@buyer.com</Identity>
			<SharedSecret>abracadabra</SharedSecret>
			</Credential>
			<UserAgent>Network Hub 1.1</UserAgent>
			</Sender>
			</Header>
			<Request>
			<PunchOutSetupRequest operation="create">
			<BuyerCookie>nomnom</BuyerCookie>
			<BrowserFormPost>
			<URL>https://browserFormPost</URL>
			</BrowserFormPost>
			<SupplierSetup>
			<URL>https://supplierSetup</URL>
			</SupplierSetup>
			</PunchOutSetupRequest>
			</Request>
			</cXML>';

		$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
	}

	public function testSimpleMessage(): void
	{
		$from = new Party(
			new Credential('AribaNetworkUserId', 'admin@acme.com')
		);
		$to = new Party(
			new Credential('DUNS', '012345678')
		);
		$sender = new Party(
			new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
			'Network Hub 1.1'
		);
		$message = new Message(
			new PunchOutOrderMessage(
				'34234234ADFSDF234234',
				new PunchOutOrderMessageHeader(new MoneyWrapper('USD', 76320))
			)
		);

		$header = new Header(
			$from,
			$to,
			$sender
		);

		$msg = CXml::forMessage(
			new PayloadIdentity('payload-id', new \DateTime('2000-01-01')),
			$message,
			$header
		);

		$actualXml = Serializer::create()->serialize($msg);

		// XML *NOT* copied from cXML Reference Guide
		$expectedXml =
			'<?xml version="1.0" encoding="UTF-8"?>
			<cXML payloadID="payload-id" timestamp="2000-01-01T00:00:00+00:00">
			<Header>
			<From>
			<Credential domain="AribaNetworkUserId">
			<Identity>admin@acme.com</Identity>
			</Credential>
			</From>
			<To>
			<Credential domain="DUNS">
			<Identity>012345678</Identity>
			</Credential>
			</To>
			<Sender>
			<Credential domain="AribaNetworkUserId">
			<Identity>sysadmin@buyer.com</Identity>
			<SharedSecret>abracadabra</SharedSecret>
			</Credential>
			<UserAgent>Network Hub 1.1</UserAgent>
			</Sender>
			</Header>
			<Message>
			<PunchOutOrderMessage>
			<BuyerCookie>34234234ADFSDF234234</BuyerCookie>
			<PunchOutOrderMessageHeader operationAllowed="create">
			<Total>
			<Money currency="USD">763.20</Money>
			</Total>
			</PunchOutOrderMessageHeader>
			</PunchOutOrderMessage>
			</Message>
			</cXML>';

		$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
	}

	public function testSimpleResponse(): void
	{
		$msg = CXml::forResponse(
			new PayloadIdentity(
				'978979621537--4882920031100014936@206.251.25.169',
				new \DateTime('2001-01-08T10:47:01-08:00')
			),
			new Response(
				new Status(200, 'OK', 'Ping Response CXml'),
				null
			)
		);

		$actualXml = Serializer::create()->serialize($msg);

		// XML copied from cXML Reference Guide
		$expectedXml =
			'<?xml version="1.0" encoding="UTF-8"?>
			<cXML timestamp="2001-01-08T10:47:01-08:00" payloadID="978979621537--4882920031100014936@206.251.25.169">
			<Response>
			<Status code="200" text="OK">Ping Response CXml</Status>
			</Response>
			</cXML>';

		$this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
	}

	public function testDeserialize(): void
	{
		$xml =
			'<?xml version="1.0" encoding="UTF-8"?>
			<cXML timestamp="2022-06-07T10:09:56+00:00" payloadID="x.y.z">
			<Response>
			<Status code="200" text="OK">Ping Response CXml</Status>
			</Response>
			</cXML>';

		$serializer = Serializer::create();
		$cXml = $serializer->deserialize($xml);

		$resultingXml = $serializer->serialize($cXml);

		$this->assertXmlStringEqualsXmlString($xml, $resultingXml);
	}

	/**
	 * even though the cXML definition defines the timestamp value to be in ISO-8601 format there are some providers that
	 * also uses the milliseconds value (i.e. JAGGAER)
	 */
	public function testDeserializeWithMilliseconds(): void
	{
		$xmlIn =
			'<?xml version="1.0" encoding="UTF-8"?>
			<cXML timestamp="2022-06-07T10:09:56.728+00:00" payloadID="x.y.z">
			<Response>
			<Status code="200" text="OK">Ping Response CXml</Status>
			</Response>
			</cXML>';

		$serializer = Serializer::create();
		$cXml = $serializer->deserialize($xmlIn);

		$actual = $serializer->serialize($cXml);

		$xmlOut =
			'<?xml version="1.0" encoding="UTF-8"?>
			<cXML timestamp="2022-06-07T10:09:56+00:00" payloadID="x.y.z">
			<Response>
			<Status code="200" text="OK">Ping Response CXml</Status>
			</Response>
			</cXML>';
		$this->assertXmlStringEqualsXmlString($xmlOut, $actual);
	}
}
