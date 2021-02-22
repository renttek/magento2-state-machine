<?php declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\Config;

use Magento\Framework\Config\Dom\UrnResolver;
use Renttek\StateMachine\Model\Config\SchemaLocator;
use PHPUnit\Framework\TestCase;

class SchemaLocatorTest extends TestCase
{
    public function testResulvesXsdFileByUrn()
    {
        $urnResolverMock = $this->createMock(UrnResolver::class);
        $urnResolverMock->expects(self::once())
            ->method('getRealPath')
            ->with('urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd')
            ->willReturn('');

        new SchemaLocator($urnResolverMock);
    }
}
