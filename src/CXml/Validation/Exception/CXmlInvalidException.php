<?php

declare(strict_types=1);

namespace CXml\Validation\Exception;

use CXml\Exception\CXmlNotAcceptableException;
use LibXMLError;
use Throwable;

use function sprintf;
use function trim;

class CXmlInvalidException extends CXmlNotAcceptableException
{
    public function __construct(string $message, private readonly string $xml, Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    /** @phpstan-ignore-next-line */
    public static function fromLibXmlError($libXmlError, string $xml): self
    {
        if ($libXmlError instanceof LibXMLError) {
            $message = sprintf('%s at line %d, column %d. Code %s.', trim($libXmlError->message), $libXmlError->line, $libXmlError->column, $libXmlError->code);
        } else {
            $message = 'No LibXMLError was given.';
        }

        return new self(
            $message,
            $xml,
        );
    }
}
