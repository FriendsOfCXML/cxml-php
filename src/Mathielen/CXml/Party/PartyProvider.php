<?php

namespace Mathielen\CXml\Party;

use Mathielen\CXml\Model\Party;
use PHPUnit\Framework\MockObject\Builder\Identity;

class PartyProvider implements PartyProviderInterface
{
    public function getOwnParty(): Party
    {
    }
}
