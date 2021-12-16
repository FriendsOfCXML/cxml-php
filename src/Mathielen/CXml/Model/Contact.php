<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Contact
{

	const ROLE_ENDUSER = 'endUser';
	const ROLE_ADMINISTRATOR = 'administrator';
	const ROLE_PURCHASINGAGENT = 'purchasingAgent';
	const ROLE_TECHNICALSUPPORT = 'technicalSupport';
	const ROLE_CUSTOMERSERVICE = 'customerService';
	const ROLE_SALES = 'sales';
	const ROLE_SUPPLIERCORPORATE = 'supplierCorporate';
	const ROLE_SUPPLIERMASTERACCOUNT = 'supplierMasterAccount';
	const ROLE_SUPPLIERACCOUNT = 'supplierAccount';
	const ROLE_BUYERCORPORATE = 'buyerCorporate';
	const ROLE_BUYERMASTERACCOUNT = 'buyerMasterAccount';
	const ROLE_BUYERACCOUNT = 'buyerAccount';
	const ROLE_BUYER = 'buyer';
	const ROLE_SUBSEQUENTBUYER = 'subsequentBuyer';

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
	 * @Ser\SerializedName("Email")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $email = null;

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

	public function getRole(): ?string
	{
		return $this->role;
	}

	public function getName(): MultilanguageString
	{
		return $this->name;
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

	public function getExtrinsics(): array
	{
		return $this->extrinsics;
	}

	public function addEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}
}
