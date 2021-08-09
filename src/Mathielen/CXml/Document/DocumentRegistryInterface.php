<?php

namespace Mathielen\CXml\Document;

use Mathielen\CXml\Model\CXml;

interface DocumentRegistryInterface
{
	public function register(CXml $cxml): void;

	public function getByDocumentReference(string $documentReferencePayloadId): ?CXml;
}
