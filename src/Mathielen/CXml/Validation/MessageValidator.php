<?php

namespace Mathielen\CXml\Validation;

use Assert\Assertion;
use Mathielen\CXml\Validation\Exception\InvalidCxmlException;

class MessageValidator
{
    private string $pathToDtd;

    public function __construct(string $pathToDtd)
    {
        Assertion::directory($pathToDtd);

        $this->pathToDtd = $pathToDtd;
    }

    public function validate(string $xml): void
    {
        //disable throwing of php errors
        $internalErrors = libxml_use_internal_errors(true);

        $old = new \DOMDocument();
        $old->loadXML($xml);

        $dtdinjectedDomDocument = $this->injectDtd($old, );
        if (!$dtdinjectedDomDocument->validate()) {
            throw InvalidCxmlException::fromLibXmlError(libxml_get_last_error(), $xml);
        }

        libxml_use_internal_errors($internalErrors);
    }

    private function injectDtd(\DOMDocument $originalDomDocument): \DOMDocument
    {
        $creator = new \DOMImplementation();
        $doctype = $creator->createDocumentType('cXML', null, $this->pathToDtd.'/cXML.dtd');
        $new = $creator->createDocument(null, null, $doctype);
        $new->encoding = "utf-8";

        $oldNode = $originalDomDocument->getElementsByTagName('cXML')->item(0);
        if (!$oldNode) {
            throw new InvalidCxmlException("Missing cXML root node", $originalDomDocument->saveXML());
        }

        $newNode = $new->importNode($oldNode, true);
        $new->appendChild($newNode);

        return $new;
    }
}
