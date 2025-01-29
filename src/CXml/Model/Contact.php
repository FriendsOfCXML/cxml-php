<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\ExtrinsicsTrait;
use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['name', 'postalAddress', 'email', 'phone', 'fax', 'url', 'extrinsics', 'idReferences'])]
class Contact
{
    use ExtrinsicsTrait;
    use IdReferencesTrait;

    final public const ROLE_ENDUSER = 'endUser';

    final public const ROLE_ADMINISTRATOR = 'administrator';

    final public const ROLE_PURCHASINGAGENT = 'purchasingAgent';

    final public const ROLE_TECHNICALSUPPORT = 'technicalSupport';

    final public const ROLE_CUSTOMERSERVICE = 'customerService';

    final public const ROLE_SALES = 'sales';

    final public const ROLE_SUPPLIERCORPORATE = 'supplierCorporate';

    final public const ROLE_SUPPLIERMASTERACCOUNT = 'supplierMasterAccount';

    final public const ROLE_SUPPLIERACCOUNT = 'supplierAccount';

    final public const ROLE_BUYERCORPORATE = 'buyerCorporate';

    final public const ROLE_BUYERMASTERACCOUNT = 'buyerMasterAccount';

    final public const ROLE_BUYERACCOUNT = 'buyerAccount';

    final public const ROLE_BUYER = 'buyer';

    final public const ROLE_SUBSEQUENTBUYER = 'subsequentBuyer';

    #[Serializer\SerializedName('Email')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $email = null;

    public function __construct(
        #[Serializer\SerializedName('Name')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly MultilanguageString $name,
        #[Serializer\XmlAttribute]
        private readonly ?string $role = null,
    ) {
    }

    public static function create(MultilanguageString $name, ?string $role = null): self
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

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
