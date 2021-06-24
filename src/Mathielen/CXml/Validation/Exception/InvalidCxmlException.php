<?php

namespace Mathielen\CXml\Validation\Exception;

use Mathielen\CXml\Exception\CXmlException;

class InvalidCxmlException extends CXmlException
{
    private string $xml;

    public function __construct(string $message, string $xml, \Throwable $previous = null)
    {
        parent::__construct($message."\nWith XML:\n".$xml, $previous);

        $this->xml = $xml;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public static function fromLibXmlError($libXmlError, string $xml): self
    {
        if ($libXmlError instanceof \LibXMLError) {
            $message = sprintf('%s at line %d, column %d. Code %s.', $libXmlError->message, $libXmlError->line, $libXmlError->column, $libXmlError->code);
        } else {
            $message = 'No LibXMLError was given.';
        }

        return new self(
            $message,
            $xml
        );
    }
}
