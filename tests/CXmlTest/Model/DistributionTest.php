<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Model\Distribution;
use CXml\Model\Request\OrderRequest;
use CXml\Serializer;
use CXml\Validation\DtdValidator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function str_replace;

/**
 * @internal
 */
#[CoversNothing]
final class DistributionTest extends TestCase
{
    public function testDeserializeDistribution(): void
    {
        $distributionXml = <<<'XML'
            </ItemDetail>
            <Distribution>
                <Accounting name="DistributionCharge">
                    <AccountingSegment id="CC-0042">
                        <Name xml:lang="en">Cost Center</Name>
                        <Description xml:lang="en">IT Department</Description>
                    </AccountingSegment>
                    <AccountingSegment id="0815">
                        <Name xml:lang="en">GL Account</Name>
                        <Description xml:lang="en">Office Supplies</Description>
                    </AccountingSegment>
                </Accounting>
                <Charge>
                    <Money currency="USD">7.30</Money>
                </Charge>
            </Distribution>
            XML;

        $xml = (string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/OrderRequest.xml');
        $xml = str_replace('</ItemDetail>', $distributionXml, $xml);

        // make sure we are testing a valid document
        DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.063')->validateAgainstDtd($xml);

        $cxml = Serializer::create()->deserialize($xml);

        /** @var OrderRequest $orderRequest */
        $orderRequest = $cxml->request?->payload;
        $distribution = $orderRequest->getItems()[0]->distribution;

        $this->assertInstanceOf(Distribution::class, $distribution);
        $this->assertSame('DistributionCharge', $distribution->accounting->name);
        $this->assertSame('USD', $distribution->charge->money->currency);
        $this->assertSame(730, $distribution->charge->money->getValueCent());

        $segments = $distribution->accounting->accountingSegments;
        $this->assertCount(2, $segments);
        $this->assertSame('CC-0042', $segments[0]->id);
        $this->assertSame('Cost Center', $segments[0]->name->value);
        $this->assertSame('IT Department', $segments[0]->description->value);
        $this->assertSame('0815', $segments[1]->id, 'leading zeros must be kept');

        // serializing it again must result in the same (valid) document
        $reserialized = Serializer::create()->serialize($cxml);
        DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.063')->validateAgainstDtd($reserialized);
        $this->assertStringContainsString('<AccountingSegment id="0815">', $reserialized);
        $this->assertMatchesRegularExpression('#</Accounting>\s*<Charge>\s*<Money currency="USD">7.30</Money>\s*</Charge>\s*</Distribution>#', $reserialized);
    }
}
