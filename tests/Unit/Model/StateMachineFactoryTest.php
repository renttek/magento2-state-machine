<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\Config;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateFactory;
use Renttek\StateMachine\Model\StateMachine\TransitionFactory;
use Renttek\StateMachine\Model\StateMachineFactory;
use function PHPUnit\Framework\assertEquals;

class StateMachineFactoryTest extends TestCase
{
    private MockObject&Config $configMock;
    private MockObject&StateFactory $stateFactoryMock;
    private MockObject&TransitionFactory $transitionFactoryMock;
    private MockObject&State $stateMock;
    private StateMachineFactory $stateMachineFactory;

    protected function setUp(): void
    {
        $this->configMock            = $this->createMock(Config::class);
        $this->stateFactoryMock      = $this->createMock(StateFactory::class);
        $this->transitionFactoryMock = $this->createMock(TransitionFactory::class);
        $this->stateMock             = $this->createMock(State::class);

        $this->stateFactoryMock
            ->method('createFromConfig')
            ->willReturn($this->stateMock);

        $this->stateMachineFactory = new StateMachineFactory(
            $this->configMock,
            $this->stateFactoryMock,
            $this->transitionFactoryMock
        );
    }

    public function testFetchesStateMachineConfigFromConfigModel(): void
    {
        $this->stateMock
            ->method('getName')
            ->willReturn('foo');

        $this->configMock
            ->expects($this->once())
            ->method('getStateMachine')
            ->with('test')
            ->willReturn(
                [
                    'initial_state' => 'foo',
                    'states'        => [
                        'foo' => [],
                    ],
                    'transitions'   => [],
                ]
            );

        $this->stateMachineFactory->create('test');
    }

    public function testCreatesStatesByStateFactory(): void
    {
        $stateConfig = [
            'foo' => 'bar',
        ];

        $this->stateMock
            ->method('getName')
            ->willReturn('foo');

        $this->stateFactoryMock
            ->expects($this->once())
            ->method('createFromConfig')
            ->with($stateConfig);

        $this->configMock
            ->method('getStateMachine')
            ->willReturn(
                [
                    'initial_state' => 'foo',
                    'states'        => [
                        'foo' => $stateConfig,
                    ],
                    'transitions'   => [],
                ]
            );

        $this->stateMachineFactory->create('test');
    }

    public function testCreatesTransitionsByTransitionFactory(): void
    {
        $transitionConfig = [
            'foo' => 'bar',
        ];

        $this->stateMock
            ->method('getName')
            ->willReturn('foo');

        $this->transitionFactoryMock
            ->expects($this->once())
            ->method('createFromConfig')
            ->with($transitionConfig);

        $this->configMock
            ->method('getStateMachine')
            ->willReturn(
                [
                    'initial_state' => 'foo',
                    'states'        => [
                        'foo' => [],
                    ],
                    'transitions'   => [
                        'bar' => $transitionConfig,
                    ],
                ]
            );

        $this->stateMachineFactory->create('test');
    }

    public function testGetReturnsTheSameObjectWhenCalledMultipleTimesWithTheSameName(): void
    {
        $transitionConfig = [
            'foo' => 'bar',
        ];

        $this->stateMock
            ->method('getName')
            ->willReturn('foo');

        $this->transitionFactoryMock
            ->expects($this->once())
            ->method('createFromConfig')
            ->with($transitionConfig);

        $this->configMock
            ->method('getStateMachine')
            ->willReturn(
                [
                    'initial_state' => 'foo',
                    'states'        => [
                        'foo' => [],
                    ],
                    'transitions'   => [
                        'bar' => $transitionConfig,
                    ],
                ]
            );

        assertEquals(
            $this->stateMachineFactory->get('test'),
            $this->stateMachineFactory->get('test')
        );
    }
}
