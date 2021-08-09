<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Contact
{

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $role;

	/**
	 * @Ser\SerializedName("Name")
	 * @Ser\XmlElement (cdata=false)
	 */
	private MultilanguageString $name;

	/**
	 * @Ser\XmlList(inline=true, entry="IdReference")
	 * @Ser\Type("array<Mathielen\CXml\Model\IdReference>")
	 *
	 * @var IdReference[]
	 */
	private array $idReferences = [];

	/**
	 * @Ser\XmlList(inline=true, entry="Extrinsic")
	 * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
	 *
	 * @var Extrinsic[]
	 */
	private array $extrinsics = [];

	public function __construct(MultilanguageString $name, ?string $role = null)
	{
		$this->role = $role;
		$this->name = $name;
	}

	public static function create(MultilanguageString $name, ?string $role = null): self
	{
		return new self($name, $role);
	}

	public function addIdReference(string $domain, string $identifier): self
	{
		$this->idReferences[] = new IdReference($domain, $identifier);

		return $this;
	}

	public function addExtrinsic(Extrinsic $extrinsic): self
	{
		$this->extrinsics[] = $extrinsic;

		return $this;
	}

}