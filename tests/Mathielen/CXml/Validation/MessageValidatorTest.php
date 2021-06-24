<?php

namespace Mathielen\CXml\Validation;

use Mathielen\CXml\Validation\Exception\InvalidCxmlException;
use PHPStan\Testing\TestCase;

class MessageValidatorTest extends TestCase
{
    private MessageValidator $sut;

    public function setUp(): void
    {
        $this->sut = new MessageValidator('tests/metadata/cxml/dtd');
    }

    public function testValidateSuccess(): void
    {
        $this->expectNotToPerformAssertions();

        $xml = file_get_contents('tests/metadata/cxml/samples/simple-profile-request.xml');
        $this->sut->validate($xml);
    }

    public function testValidateMissingRootNode(): void
    {
        $this->expectException(InvalidCxmlException::class);

        $xml = '<some-node></some-node>';
        $this->sut->validate($xml);
    }

    public function testValidateInvalid(): void
    {
        $this->expectException(InvalidCxmlException::class);

        $xml = '<cXML></cXML>';
        $this->sut->validate($xml);
    }
}
