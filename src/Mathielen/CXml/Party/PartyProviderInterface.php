<?php

namespace Mathielen\CXml\Party;

use Mathielen\CXml\Model\Party;
use PHPUnit\Framework\MockObject\Builder\Identity;

interface PartyProviderInterface
{
    public function getOwnParty(): Party;
}
