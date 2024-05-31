<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['name', 'postalAddress', 'email', 'phone', 'fax', 'url', 'extrinsics', 'idReferences'])]
class Contact
{
    use ExtrinsicsTrait;
    use IdReferencesTrait;

    public final const ROLE_ENDUSER = 'endUser';

    public final const ROLE_ADMINISTRATOR = 'administrator';

    public final const ROLE_PURCHASINGAGENT = 'purchasingAgent';

    public final const ROLE_TECHNICALSUPPORT = 'technicalSupport';

    public final const ROLE_CUSTOMERSERVICE = 'customerService';

    public final const ROLE_SALES = 'sales';

    public final const ROLE_SUPPLIERCORPORATE = 'supplierCorporate';

    public final const ROLE_SUPPLIERMASTERACCOUNT = 'supplierMasterAccount';

    public final const ROLE_SUPPLIERACCOUNT = 'supplierAccount';

    public final const ROLE_BUYERCORPORATE = 'buyerCorporate';

    public final const ROLE_BUYERMASTERACCOUNT = 'buyerMasterAccount';

    public final const ROLE_BUYERACCOUNT = 'buyerAccount';

    public final const ROLE_BUYER = 'buyer';

    public final const ROLE_SUBSEQUENTBUYER = 'subsequentBuyer';

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

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
