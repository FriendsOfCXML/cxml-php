<?php

namespace CXmlTest\Payload;

use CXml\Payload\DefaultPayloadIdentityFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DefaultPayloadIdentityFactoryTest extends TestCase
{
    public function testGenerateNewPayloadId(): void
    {
        $pif = new DefaultPayloadIdentityFactory(function (): \DateTime|false {
            // 2022-04-22 08:00:00.400000 +00:00
            return \DateTime::createFromFormat('U.v', '1650614400.400');
        });
        $actualIdentity = $pif->newPayloadIdentity();

        $this->assertStringStartsWith('1650614400.400', $actualIdentity->getPayloadId());
    }
}
