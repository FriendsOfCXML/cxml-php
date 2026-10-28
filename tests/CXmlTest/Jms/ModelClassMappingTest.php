<?php

declare(strict_types=1);

namespace CXmlTest\Jms;

use CXml\Exception\CXmlNotImplementedException;
use CXml\Jms\ModelClassMapping;
use CXml\Model\Exception\CXmlModelNotFoundException;
use CXml\Model\Extension\PaymentReference;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Request\OrderRequest;
use CXml\Model\Response\ProfileResponse;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ModelClassMappingTest extends TestCase
{
    #[DataProvider('provideKnownModels')]
    public function testFindClassForSerializedName(string $serializedName, string $expectedClass): void
    {
        $this->assertSame($expectedClass, ModelClassMapping::fromDefaultModelPath()->findClassForSerializedName($serializedName));
    }

    public static function provideKnownModels(): iterable
    {
        yield ['OrderRequest', OrderRequest::class];
        yield ['PunchOutOrderMessage', PunchOutOrderMessage::class];
        yield ['ProfileResponse', ProfileResponse::class];
        yield ['PaymentReference', PaymentReference::class];
    }

    #[DataProvider('provideNonModels')]
    public function testUnknownSerializedName(string $serializedName): void
    {
        try {
            ModelClassMapping::fromDefaultModelPath()->findClassForSerializedName($serializedName);
            $this->fail('Expected CXmlModelNotFoundException');
        } catch (CXmlModelNotFoundException $cXmlModelNotFoundException) {
            $this->assertInstanceOf(CXmlNotImplementedException::class, $cXmlModelNotFoundException);
            $this->assertSame('Model not found for cXML-node: ' . $serializedName, $cXmlModelNotFoundException->getMessage());
        }
    }

    public static function provideNonModels(): iterable
    {
        yield 'unknown' => ['DoesNotExist'];
        yield 'interface' => ['PayloadInterface'];
        yield 'trait' => ['CommentsTrait'];
        yield 'exception' => ['CXmlModelNotFoundException'];
    }
}
