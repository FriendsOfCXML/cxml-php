<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Builder\OrderRequestBuilder;
use CXml\Model\Address;
use CXml\Model\BillTo;
use CXml\Model\Country;
use CXml\Model\CountryCode;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Date;
use CXml\Model\Extension\PaymentReference;
use CXml\Model\Header;
use CXml\Model\Message\Message;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\Money;
use CXml\Model\MoneyWrapper;
use CXml\Model\MultilanguageString;
use CXml\Model\Party;
use CXml\Model\PayloadIdentity;
use CXml\Model\Payment;
use CXml\Model\Phone;
use CXml\Model\PostalAddress;
use CXml\Model\Request\OrderRequest;
use CXml\Model\Request\OrderRequestHeader;
use CXml\Model\Request\PunchOutSetupRequest;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use CXml\Model\ShipTo;
use CXml\Model\Status;
use CXml\Model\TelephoneNumber;
use CXml\Serializer;
use DateTime;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
#[CoversNothing]
final class SerializerTest extends TestCase
{
    public function testSerializeSimpleRequest(): void
    {
        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com'),
        );
        $to = new Party(
            new Credential('DUNS', '012345678'),
        );
        $sender = new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            'Network Hub 1.1',
        );
        $request = new Request(
            new PunchOutSetupRequest(
                'nomnom',
                'https://browserFormPost',
                'https://supplierSetup',
            ),
        );

        $header = new Header(
            $from,
            $to,
            $sender,
        );

        $msg = CXml::forRequest(
            new PayloadIdentity('payload-id', new DateTime('2000-01-01')),
            $request,
            $header,
        );

        $actualXml = Serializer::create()->serialize($msg);

        // XML copied from cXML Reference Guide
        $expectedXml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
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
            </cXML>
            XML;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testSerializeSimpleMessage(): void
    {
        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com'),
        );
        $to = new Party(
            new Credential('DUNS', '012345678'),
        );
        $sender = new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            'Network Hub 1.1',
        );
        $message = new Message(
            PunchOutOrderMessage::create(
                '34234234ADFSDF234234',
                new PunchOutOrderMessageHeader(new MoneyWrapper('USD', 76320)),
            ),
        );

        $header = new Header(
            $from,
            $to,
            $sender,
        );

        $msg = CXml::forMessage(
            new PayloadIdentity('payload-id', new DateTime('2000-01-01')),
            $message,
            $header,
        );

        $actualXml = Serializer::create()->serialize($msg);

        // XML *NOT* copied from cXML Reference Guide
        $expectedXml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
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
            </cXML>
            XML;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testSerializeSimpleResponse(): void
    {
        $msg = CXml::forResponse(
            new PayloadIdentity(
                '978979621537--4882920031100014936@206.251.25.169',
                new DateTime('2001-01-08T10:47:01-08:00'),
            ),
            new Response(
                new Status(200, 'OK', 'Ping Response CXml'),
            ),
        );

        $actualXml = Serializer::create()->serialize($msg);

        // XML copied from cXML Reference Guide
        $expectedXml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML timestamp="2001-01-08T10:47:01-08:00" payloadID="978979621537--4882920031100014936@206.251.25.169">
            	<Response>
            		<Status code="200" text="OK">Ping Response CXml</Status>
            	</Response>
            </cXML>
            XML;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testDeserialize(): void
    {
        $xml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML timestamp="2022-06-07T10:09:56+00:00" payloadID="x.y.z">
            	<Response>
            		<Status code="200" text="OK">Ping Response CXml</Status>
            	</Response>
            </cXML>
            XML;

        $serializer = Serializer::create();
        $cXml = $serializer->deserialize($xml);

        $resultingXml = $serializer->serialize($cXml);

        $this->assertXmlStringEqualsXmlString($xml, $resultingXml);
    }

    /**
     * even though the cXML definition defines the timestamp value to be in ISO-8601 format there are some providers that
     * also uses the milliseconds value (i.e. JAGGAER).
     */
    public function testDeserializeWithMillisecondsAndTimezone(): void
    {
        $xmlIn = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML timestamp="2022-06-07T10:09:56.728+00:00" payloadID="x.y.z">
            	<Response>
            		<Status code="200" text="OK">Ping Response CXml</Status>
            	</Response>
            </cXML>
            XML;

        $serializer = Serializer::create();
        $cXml = $serializer->deserialize($xmlIn);

        $actual = $serializer->serialize($cXml);
        $xmlOut = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML timestamp="2022-06-07T10:09:56+00:00" payloadID="x.y.z">
            	<Response>
            		<Status code="200" text="OK">Ping Response CXml</Status>
            	</Response>
            </cXML>
            XML;
        $this->assertXmlStringEqualsXmlString($xmlOut, $actual);
    }

    public function testDeserializeWithMillisecondsNoTimezone(): void
    {
        $xmlIn = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML timestamp="2022-06-07T10:09:56.728" payloadID="x.y.z">
            	<Response>
            		<Status code="200" text="OK">Ping Response CXml</Status>
            	</Response>
            </cXML>
            XML;

        $serializer = Serializer::create();
        $cXml = $serializer->deserialize($xmlIn);

        $this->assertSame('2022-06-07T10:09:56+00:00', $cXml->timestamp->format('c'));
    }

    public function testDeserializeWithDateTimeForDate(): void
    {
        $xmlIn = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.044/cXML.dtd">
            <cXML payloadID="1676913078755.23986034.000017504@6/lkmlPq0GFws44XIhyDt9yjJb8=" timestamp="2023-02-20T09:11:18-08:00" version="1.2.044" xml:lang="en-US">
            	<Header></Header>
            	<Request deploymentMode="test">
            		<OrderRequest>
            			<ItemOut quantity="1" requestedDeliveryDate="2023-02-25T02:30:00-08:00" lineNumber="1"></ItemOut>
            			<ItemOut quantity="2" requestedDeliveryDate="2023-02-26" lineNumber="2"></ItemOut>
            			<ItemOut quantity="3" lineNumber="3"></ItemOut>
            		</OrderRequest>
            	</Request>
            </cXML>
            XML;

        $serializer = Serializer::create();
        $cXml = $serializer->deserialize($xmlIn);

        /** @var OrderRequest $orderRequest */
        $orderRequest = $cXml->request->payload;

        $this->assertSame('2023-02-25 02:30:00', $orderRequest->getItems()[0]->requestedDeliveryDate->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(DateTime::class, $orderRequest->getItems()[0]->requestedDeliveryDate);

        $this->assertSame('2023-02-26', $orderRequest->getItems()[1]->requestedDeliveryDate->format('Y-m-d'));
        $this->assertInstanceOf(Date::class, $orderRequest->getItems()[1]->requestedDeliveryDate);

        $this->assertNull($orderRequest->getItems()[2]->requestedDeliveryDate);
    }

    public function testDeserializeInvalidDate(): void
    {
        $this->expectException(RuntimeException::class);

        $xmlIn = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.044/cXML.dtd">
            <cXML payloadID="1676913078755.23986034.000017504@6/lkmlPq0GFws44XIhyDt9yjJb8=" timestamp="2023-02-20T09:11:18-08:00" version="1.2.044" xml:lang="en-US">
            	<Header></Header>
            	<Request deploymentMode="test">
            		<OrderRequest>
            			<ItemOut quantity="1" requestedDeliveryDate="invalid" lineNumber="1"></ItemOut>
            		</OrderRequest>
            	</Request>
            </cXML>
            XML;

        $serializer = Serializer::create();
        $serializer->deserialize($xmlIn);
    }

    public function testSerializeDateOnly(): void
    {
        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com'),
        );
        $to = new Party(
            new Credential('DUNS', '012345678'),
        );
        $sender = new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            'Network Hub 1.1',
        );

        $orderDate = new Date('2000-01-01');

        $orderRequest =
            OrderRequestBuilder::create('order-id', $orderDate, 'EUR')
                ->billTo('name')
                ->build();

        $header = new Header(
            $from,
            $to,
            $sender,
        );

        $msg = CXml::forRequest(
            new PayloadIdentity('payload-id', new DateTime('2000-01-01')),
            new Request(
                $orderRequest,
            ),
            $header,
        );

        $actualXml = Serializer::create()->serialize($msg);

        $expectedXml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
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
            		<OrderRequest>
            			<OrderRequestHeader orderDate="2000-01-01" orderID="order-id" type="new">
            				<Total>
            					<Money currency="EUR">0.00</Money>
            				</Total>
            				<BillTo>
            					<Address>
            						<Name xml:lang="en">name</Name>
            					</Address>
            				</BillTo>
            			</OrderRequestHeader>
            		</OrderRequest>
            	</Request>
            </cXML>
            XML;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testDeserializeOneRowXml(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.1.008/cXML.dtd"><cXML timestamp="2022-06-07T10:09:56+00:00" payloadID="x.y.z"><Response><Status code="200" text="OK">Ping Response CXml</Status></Response></cXML>';

        $serializer = Serializer::create();
        $cXml = $serializer->deserialize($xml);

        $resultingXml = $serializer->serialize($cXml);

        $this->assertXmlStringEqualsXmlString($xml, $resultingXml);
    }

    public function testDeserializeNullProperty(): void
    {
        $xml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
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
            		<OrderRequest>
            			<OrderRequestHeader orderDate="2000-01-01" orderID="order-id" type="new">
            				<Total>
            					<Money currency="EUR">0.00</Money>
            				</Total>
            				<BillTo>
            					<Address>
            						<Name xml:lang="en">name</Name>
            					</Address>
            				</BillTo>
            			</OrderRequestHeader>
            		</OrderRequest>
            	</Request>
            </cXML>
            XML;

        $cxml = Serializer::create()->deserialize($xml);

        /** @var OrderRequest $orderRequest */
        $orderRequest = $cxml->request->payload;

        // Error: Typed property CXml\Model\Request\OrderRequestHeader::$shipTo must not be accessed before initialization
        $shipTo = $orderRequest->orderRequestHeader->getShipTo();
        $this->assertNotInstanceOf(ShipTo::class, $shipTo);
    }

    public function testSerializePayment(): void
    {
        $from = new Party(
            new Credential('AribaNetworkUserId', 'admin@acme.com'),
        );
        $to = new Party(
            new Credential('DUNS', '012345678'),
        );
        $sender = new Party(
            new Credential('AribaNetworkUserId', 'sysadmin@buyer.com', 'abracadabra'),
            'Network Hub 1.1',
        );

        $orderRequestHeader = OrderRequestHeader::create(
            'DO1234',
            new DateTime('2000-10-12T18:41:29-08:00'),
            null,
            new BillTo(
                new Address(
                    new MultilanguageString('Acme GmbH'),
                    new PostalAddress(
                        [],
                        [
                            'Acme Street 18',
                        ],
                        'Solingen',
                        new Country('DE', 'Deutschland'),
                        null,
                        null,
                        '42699',
                        'default',
                    ),
                    null,
                    null,
                    null,
                    new Phone(
                        new TelephoneNumber(
                            new CountryCode('DE', '49'),
                            '761',
                            '1234567',
                        ),
                        'company',
                    ),
                ),
            ),
            new MoneyWrapper(
                'EUR',
                8500,
            ),
        );

        $payment1 = PaymentReference::create(
            new Money('EUR', 1000),
            'voucher',
        )
            ->addIdReference('code', 'ABC123');

        $payment2 = PaymentReference::create(
            new Money('EUR', 1000),
            'creditcard',
            'stripe',
        )
            ->addIdReference('charge-id', 'ch...')
            ->addExtrinsicAsKeyValue('some', 'value');

        $payment = new Payment(
            [
                $payment1,
                $payment2,
            ],
        );
        $orderRequestHeader->setPayment($payment);

        $orderRequest = OrderRequest::create(
            $orderRequestHeader,
        );

        $request = new Request(
            $orderRequest,
        );

        $header = new Header(
            $from,
            $to,
            $sender,
        );

        $msg = CXml::forRequest(
            new PayloadIdentity('payload-id', new DateTime('2000-01-01')),
            $request,
            $header,
        );

        $actualXml = Serializer::create()->serialize($msg);

        // XML copied from cXML Reference Guide
        $expectedXml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
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
            		<OrderRequest>
            			<OrderRequestHeader orderDate="2000-10-12T18:41:29-08:00" orderID="DO1234" type="new">
            				<Total>
            					<Money currency="EUR">85.00</Money>
            				</Total>
            				<BillTo>
            					<Address>
            						<Name xml:lang="en">Acme GmbH</Name>
            						<PostalAddress name="default">
            							<Street>Acme Street 18</Street>
            							<City>Solingen</City>
            							<PostalCode>42699</PostalCode>
            							<Country isoCountryCode="DE">Deutschland</Country>
            						</PostalAddress>
            						<Phone name="company">
            							<TelephoneNumber>
            								<CountryCode isoCountryCode="DE">49</CountryCode>
            								<AreaOrCityCode>761</AreaOrCityCode>
            								<Number>1234567</Number>
            							</TelephoneNumber>
            						</Phone>
            					</Address>
            				</BillTo>
            				<Payment>
            					<PaymentReference method="voucher">
            						<Money currency="EUR">10.00</Money>
            						<IdReference domain="code" identifier="ABC123"/>
            					</PaymentReference>
            					<PaymentReference method="creditcard" provider="stripe">
            						<Money currency="EUR">10.00</Money>
            						<IdReference domain="charge-id" identifier="ch..."/>
            						<Extrinsic name="some">value</Extrinsic>
            					</PaymentReference>
            				</Payment>
            			</OrderRequestHeader>
            		</OrderRequest>
            	</Request>
            </cXML>
            XML;

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }

    public function testDeserializePayment(): void
    {
        $xml = <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
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
            		<OrderRequest>
            			<OrderRequestHeader orderDate="2000-10-12T18:41:29-08:00" orderID="DO1234" type="new">
            				<Total>
            					<Money currency="EUR">85.00</Money>
            				</Total>
            				<BillTo>
            					<Address>
            						<Name xml:lang="en">Acme GmbH</Name>
            						<PostalAddress name="default">
            							<Street>Acme Street 18</Street>
            							<City>Solingen</City>
            							<PostalCode>42699</PostalCode>
            							<Country isoCountryCode="DE">Deutschland</Country>
            						</PostalAddress>
            						<Phone name="company">
            							<TelephoneNumber>
            								<CountryCode isoCountryCode="DE">49</CountryCode>
            								<AreaOrCityCode>761</AreaOrCityCode>
            								<Number>1234567</Number>
            							</TelephoneNumber>
            						</Phone>
            					</Address>
            				</BillTo>
            				<Payment>
            					<PaymentReference method="voucher">
            						<Money currency="EUR">10.00</Money>
            						<IdReference domain="code" identifier="ABC123"/>
            					</PaymentReference>
            					<PaymentReference method="creditcard" provider="stripe">
            						<Money currency="EUR">10.00</Money>
            						<IdReference domain="charge-id" identifier="ch..."/>
            						<Extrinsic name="some">value</Extrinsic>
            					</PaymentReference>
            				</Payment>
            			</OrderRequestHeader>
            		</OrderRequest>
            	</Request>
            </cXML>
            XML;
        $cxml = Serializer::create()->deserialize($xml);
        $deserializedPayment = $cxml->request->payload->orderRequestHeader->getPayment();

        $this->assertNotNull($deserializedPayment);
        $this->assertIsArray($deserializedPayment->paymentImpl);

        $this->assertSame('voucher', $deserializedPayment->paymentImpl[0]->method);
        $this->assertSame('creditcard', $deserializedPayment->paymentImpl[1]->method);
    }
}
