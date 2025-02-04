<?php

declare(strict_types=1);

namespace CXml\Model\Trait;

use CXml\Model\IdReference;
use JMS\Serializer\Annotation as Serializer;

trait IdReferencesTrait
{
    /**
     * @var IdReference[]
     */
    #[Serializer\XmlList(entry: 'IdReference', inline: true)]
    #[Serializer\Type('array<CXml\Model\IdReference>')]
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
            if ($idReference->domain === $domain) {
                return $idReference->identifier;
            }
        }

        return null;
    }
}
