<?php

namespace Mathielen\CXml\Model;

use Mathielen\CXml\Endpoint;
use Mathielen\CXml\Model\Request\OrderRequest;
use Mathielen\CXml\Model\Request\OrderRequestHeader;
use Mathielen\CXml\Payload\PayloadIdentityFactoryInterface;
use PHPUnit\Framework\TestCase;
use Mathielen\CXml\Builder;

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

        $orderRequestHeader = new OrderRequestHeader(
            'DO1234',
            new \DateTime('2000-10-12T18:41:29-08:00'),
            new AddressWrapper(
                new MultilanguageString('Acme'),
                new PostalAddress(
                    [
                        'Joe Smith',
                        'Mailstop M-543'
                    ],
                    [
                        '123 Anystreet'
                    ],
                    'Sunnyvale',
                    new Country('US', 'United States'),
                    null,
                    'CA',
                    '90489',
                    'default'
                )
            ),
            new AddressWrapper(
                new MultilanguageString('Zinc GmbH'),
                new PostalAddress(
                    [],
                    [
                        'An den Eichen 18'
                    ],
                    'Solingen',
                    new Country('DE', 'Deutschland'),
                    null,
                    null,
                    '42699',
                    'default'
                )
            ),
            [new Comment(null, 'delivery-note.pdf')],
            new MoneyWrapper(
                'EUR',
                8500
            )
        );

        $orderRequest = OrderRequest::create(
            $orderRequestHeader
        );

        $item = ItemOut::create(
            1,
            10,
            new ItemId('1233244'),
            new ItemDetail(
                new MultilanguageString('hello from item 1'),
                'EA',
                new MoneyWrapper(
                    'EUR',
                    210
                )
            ),
            new \DateTime('2020-02-28')
        )->addClassification('custom', 0);
        $orderRequest->addItem($item);

        $item = ItemOut::create(
            2,
            20,
            new ItemId('1233245'),
            new ItemDetail(
                new MultilanguageString('hello from item 2'),
                'EA',
                new MoneyWrapper(
                    'EUR',
                    320
                )
            ),
            new \DateTime('2020-02-28')
        )->addClassification('custom', 0);
        $orderRequest->addItem($item);

        $cxml = Builder::create(null, $this)
            ->from($from)
            ->to($to)
            ->sender($sender, 'Platform Order Fulfillment Hub')
            ->payload($orderRequest)
            ->build(Request::DEPLOYMENT_TEST);

        $xml = Endpoint::serialize($cxml);

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
