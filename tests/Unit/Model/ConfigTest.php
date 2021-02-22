<?php declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model;

use Magento\Framework\Config\DataInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Exception\StateMachineNotFoundException;
use Renttek\StateMachine\Model\Config;
use function PHPUnit\Framework\assertEquals;

class ConfigTest extends TestCase
{
    /**
     * @var DataInterface|MockObject
     */
    private DataInterface $dataMock;

    protected function setUp(): void
    {
        $this->dataMock = $this->createMock(DataInterface::class);
    }

    public function testGetStateMachinesReadsConfigFromDataInterface(): void
    {
        $this->dataMock
            ->expects(self::once())
            ->method('get')
            ->with('state_machines')
            ->willReturn([]);

        (new Config($this->dataMock))->getStateMachines();
    }

    public function testGetStateMachineThrowsExceptionIfStateMachineWithIsNotFound(): void
    {
        $this->expectException(StateMachineNotFoundException::class);

        $this->dataMock
            ->method('get')
            ->with('state_machines')
            ->willReturn([]);

        (new Config($this->dataMock))->getStateMachine('foo');
    }

    public function testGetStateMachineReturnsConfigFromDataInterface(): void
    {
        $this->dataMock
            ->method('get')
            ->with('state_machines')
            ->willReturn(['foo' => ['name' => 'foo']]);

        assertEquals(['name' => 'foo'], (new Config($this->dataMock))->getStateMachine('foo'));
    }
}
