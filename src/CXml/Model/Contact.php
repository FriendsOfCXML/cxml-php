<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Contact
{
	use ExtrinsicsTrait;
	use IdReferencesTrait;

	public const ROLE_ENDUSER = 'endUser';
	public const ROLE_ADMINISTRATOR = 'administrator';
	public const ROLE_PURCHASINGAGENT = 'purchasingAgent';
	public const ROLE_TECHNICALSUPPORT = 'technicalSupport';
	public const ROLE_CUSTOMERSERVICE = 'customerService';
	public const ROLE_SALES = 'sales';
	public const ROLE_SUPPLIERCORPORATE = 'supplierCorporate';
	public const ROLE_SUPPLIERMASTERACCOUNT = 'supplierMasterAccount';
	public const ROLE_SUPPLIERACCOUNT = 'supplierAccount';
	public const ROLE_BUYERCORPORATE = 'buyerCorporate';
	public const ROLE_BUYERMASTERACCOUNT = 'buyerMasterAccount';
	public const ROLE_BUYERACCOUNT = 'buyerAccount';
	public const ROLE_BUYER = 'buyer';
	public const ROLE_SUBSEQUENTBUYER = 'subsequentBuyer';

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $role = null;

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

	public function __construct(MultilanguageString $name, string $role = null)
	{
		$this->role = $role;
		$this->name = $name;
	}

	public static function create(MultilanguageString $name, string $role = null): self
	{
		return new self($name, $role);
	}

	public function getRole(): ?string
	{
		return $this->role;
	}

	public function getName(): MultilanguageString
	{
		return $this->name;
	}

	public function addEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}
}
