<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\Config;

use Magento\Framework\Config\Dom\UrnResolver;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\Config\SchemaLocator;

class SchemaLocatorTest extends TestCase
{
    public function testResulvesXsdFileByUrn(): void
    {
        $urnResolverMock = $this->createMock(UrnResolver::class);
        $urnResolverMock->expects($this->once())
            ->method('getRealPath')
            ->with('urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd')
            ->willReturn('');

        new SchemaLocator($urnResolverMock);
    }
}
