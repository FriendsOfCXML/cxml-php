<?php

declare(strict_types=1);

namespace CXmlTest\Builder;

use CXml\Builder;
use CXml\Builder\OrderRequestBuilder;
use CXml\Model\Credential;
use CXml\Model\PayloadIdentity;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Serializer;
use DateTime;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

/**
 * @internal
 */
#[CoversNothing]
final class OrderRequestBuilderTest extends TestCase implements PayloadIdentityFactoryInterface
{
    public function testFromPunchOutOrderMessage(): void
    {
        $serializer = Serializer::create();
        $poomXml = file_get_contents(__DIR__ . '/fixtures/poom.xml');
        $poom = $serializer->deserialize($poomXml);

        $actualOrderRequest =
            OrderRequestBuilder::fromPunchOutOrderMessage($poom->getMessage()->getPayload())
                ->billTo('name')
                ->build();

        $actualOrderRequest = Builder::create('cxml-php UserAgent', null, $this)
            ->payload($actualOrderRequest)
            ->from(new Credential('NetworkId', 'inbound@prominate-platform.com'))
            ->to(new Credential('NetworkId', 'supplier@supplier.com'))
            ->sender(new Credential('NetworkId', 'inbound@prominate-platform.com'))
            ->build();
        $actualOrderRequest = $serializer->serialize($actualOrderRequest);

        $expectedOrderRequest = file_get_contents(__DIR__ . '/fixtures/order_request.xml');

        $this->assertXmlStringEqualsXmlString($expectedOrderRequest, $actualOrderRequest);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity(
            '933695160894',
            new DateTime('2021-01-08T23:00:06-08:00'),
        );
    }
}
