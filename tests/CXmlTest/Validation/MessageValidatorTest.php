<?php

declare(strict_types=1);

namespace CXmlTest\Validation;

use CXml\Validation\DtdValidator;
use CXml\Validation\Exception\CXmlInvalidException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

/**
 * @internal
 */
#[CoversNothing]
final class MessageValidatorTest extends TestCase
{
    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = new DtdValidator(__DIR__ . '/../../metadata/cxml/dtd/1.2.063');
    }

    public function testValidateProfileRequestSuccess(): void
    {
        $this->expectNotToPerformAssertions();

        $xml = file_get_contents(__DIR__ . '/../../metadata/cxml/samples/simple-profile-request.xml');
        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function testValidateMissingRootNode(): void
    {
        $this->expectException(CXmlInvalidException::class);

        $xml = '<some-node></some-node>';
        $this->dtdValidator->validateAgainstDtd($xml);
    }

    public function testValidateInvalid(): void
    {
        $this->expectException(CXmlInvalidException::class);

        $xml = '<cXML></cXML>';
        $this->dtdValidator->validateAgainstDtd($xml);
    }
}
