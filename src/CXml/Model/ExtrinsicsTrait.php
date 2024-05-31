<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

trait ExtrinsicsTrait
{
    /**
     * @var Extrinsic[]
     */
    #[Serializer\XmlList(inline: true, entry: 'Extrinsic')]
    #[Serializer\Type('array<CXml\Model\Extrinsic>')]
    protected array $extrinsics = [];

    public function addExtrinsic(Extrinsic $extrinsic): self
    {
        $this->extrinsics[] = $extrinsic;

        return $this;
    }

    /**
     * @return Extrinsic[]
     */
    public function getExtrinsics(): array
    {
        return $this->extrinsics;
    }

    public function getExtrinsicByName(string $name): ?Extrinsic
    {
        foreach ($this->extrinsics as $extrinsic) {
            if ($extrinsic->getName() === $name) {
                return $extrinsic;
            }
        }

        return null;
    }

    /**
     * Convenience method.
     */
    public function addExtrinsicAsKeyValue(string $name, string $value): self
    {
        return $this->addExtrinsic(new Extrinsic($name, $value));
    }

    /**
     * Convenience method.
     */
    public function getExtrinsicsAsKeyValue(): array
    {
        $extrinsics = [];

        foreach ($this->getExtrinsics() as $extrinsic) {
            $extrinsics[\trim($extrinsic->getName())] = \trim($extrinsic->getValue());
        }

        return $extrinsics;
    }
}
