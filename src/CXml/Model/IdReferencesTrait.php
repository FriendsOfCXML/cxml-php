<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

trait IdReferencesTrait
{
	/**
	 * @Ser\XmlList(inline=true, entry="IdReference")
	 * @Ser\Type("array<CXml\Model\IdReference>")
	 *
	 * @var IdReference[]
	 */
	protected array $idReferences = [];

	public function addIdReference(string $domain, string $identifier): self
	{
		$this->idReferences[] = new IdReference($domain, $identifier);

		return $this;
	}

	public function getIdReferences(): array
	{
		return $this->idReferences;
	}

	public function getIdReference(string $domain): ?string
	{
		foreach ($this->idReferences as $idReference) {
			if ($idReference->getDomain() === $domain) {
				return $idReference->getIdentifier();
			}
		}

		return null;
	}
}
