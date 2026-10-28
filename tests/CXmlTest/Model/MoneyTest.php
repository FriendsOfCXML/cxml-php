<?php

declare(strict_types=1);

namespace CXmlTest\Model;

use CXml\Model\Money;
use CXml\Model\MoneyWrapper;
use CXml\Serializer;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class MoneyTest extends TestCase
{
    #[DataProvider('provideCents')]
    public function testValueIsFormattedFromCents(int $cents, string $expectedValue): void
    {
        $money = new Money('EUR', $cents);

        $this->assertSame($expectedValue, $money->value);
        $this->assertSame($cents, $money->getValueCent());
    }

    public static function provideCents(): iterable
    {
        yield 'zero' => [0, '0.00'];
        yield 'one cent' => [1, '0.01'];
        yield 'whole amount' => [10000, '100.00'];
        yield 'thousands without separator' => [123456789, '1234567.89'];
        yield 'negative' => [-1999, '-19.99'];
    }

    public function testMoneyWrapper(): void
    {
        $wrapper = new MoneyWrapper('USD', 76320);

        $this->assertSame('USD', $wrapper->money->currency);
        $this->assertSame('763.20', $wrapper->money->value);
    }

    #[DataProvider('provideDeserializedValues')]
    public function testGetValueCentOfDeserializedMoney(string $value, int $expectedCents): void
    {
        $xml = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <cXML payloadID="x" timestamp="2022-06-07T10:09:56+00:00">
                <Message>
                    <PunchOutOrderMessage>
                        <BuyerCookie>cookie</BuyerCookie>
                        <PunchOutOrderMessageHeader operationAllowed="create">
                            <Total>
                                <Money currency="EUR">{$value}</Money>
                            </Total>
                        </PunchOutOrderMessageHeader>
                    </PunchOutOrderMessage>
                </Message>
            </cXML>
            XML;

        $cxml = Serializer::create()->deserialize($xml);

        /** @var \CXml\Model\Message\PunchOutOrderMessage $punchOutOrderMessage */
        $punchOutOrderMessage = $cxml->message?->payload;
        $money = $punchOutOrderMessage->punchOutOrderMessageHeader->total->money;

        $this->assertSame($value, $money->value);
        $this->assertSame($expectedCents, $money->getValueCent());
    }

    public static function provideDeserializedValues(): iterable
    {
        // these are all values that suffer from floating point imprecision when multiplied by 100
        yield ['19.99', 1999];
        yield ['0.29', 29];
        yield ['4.35', 435];
        yield ['1.15', 115];
        yield ['8.20', 820];
        yield ['1234567.89', 123456789];
        yield ['-19.99', -1999];
        yield ['100', 10000];
        yield ['0.00', 0];
    }
}
