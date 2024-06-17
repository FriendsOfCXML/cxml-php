<?php

declare(strict_types=1);

namespace CXml\Document;

use CXml\Model\CXml;

interface DocumentRegistryInterface
{
    public function register(CXml $cxml): void;

    public function getByDocumentReference(string $documentReferencePayloadId): ?CXml;
}
