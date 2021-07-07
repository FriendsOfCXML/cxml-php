<?php

namespace Mathielen\CXml\Validation\Exception;

use Mathielen\CXml\Exception\CXmlNotAcceptableException;

class CxmlInvalidException extends CXmlNotAcceptableException
{
    private string $xml;

    public function __construct(string $message, string $xml, \Throwable $previous = null)
    {
        parent::__construct($message, $previous);

        $this->xml = $xml;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public static function fromLibXmlError($libXmlError, string $xml): self
    {
        if ($libXmlError instanceof \LibXMLError) {
            $message = sprintf('%s at line %d, column %d. Code %s.', trim($libXmlError->message), $libXmlError->line, $libXmlError->column, $libXmlError->code);
        } else {
            $message = 'No LibXMLError was given.';
        }

        return new self(
            $message,
            $xml
        );
    }
}
