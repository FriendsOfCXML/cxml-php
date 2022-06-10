<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

trait ExtrinsicsTrait
{
	/**
	 * @Ser\XmlList(inline=true, entry="Extrinsic")
	 * @Ser\Type("array<CXml\Model\Extrinsic>")
	 *
	 * @var Extrinsic[]
	 */
	protected array $extrinsics = [];

	public function addExtrinsic(string $name, string $value): self
	{
		$this->extrinsics[] = new Extrinsic($name, $value);

		return $this;
	}

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

	public function getExtrinsicsAsKeyValue(): array
	{
		$extrinsics = [];

		foreach ($this->getExtrinsics() as $extrinsic) {
			$extrinsics[$extrinsic->getName()] = $extrinsic->getValue();
		}

		return $extrinsics;
	}
}
