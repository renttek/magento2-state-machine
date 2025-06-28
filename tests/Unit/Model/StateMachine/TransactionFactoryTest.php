<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\StateMachine\Label;
use Renttek\StateMachine\Model\StateMachine\LabelFactory;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\TransitionFactory;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

class TransactionFactoryTest extends TestCase
{
    private MockObject&StateList $stateListMock;
    private MockObject&State $stateMock;
    private TransitionFactory $transitionFactory;

    protected function setUp(): void
    {
        $labelFactoryMock       = $this->createMock(LabelFactory::class);
        $labelMock              = $this->createMock(Label::class);
        $this->stateListMock    = $this->createMock(StateList::class);
        $this->stateMock        = $this->createMock(State::class);

        $labelFactoryMock
            ->method('createFromConfig')
            ->willReturn($labelMock);

        $this->stateListMock
            ->method('getState')
            ->willReturn($this->stateMock);

        $this->transitionFactory = new TransitionFactory($labelFactoryMock);
    }

    public function testPassesNameFromConfigToTransition(): void
    {
        $config = [
            'name' => 'my-transition',
            'to'   => '',
            'from' => [],
        ];

        $transition = $this->transitionFactory->createFromConfig($config, $this->stateListMock);

        assertEquals('my-transition', $transition->getName());
    }

    public function testGetsTargetStateFromStateList(): void
    {
        $config = [
            'name' => '',
            'to'   => 'foo',
            'from' => [],
        ];

        $this->stateListMock
            ->expects($this->once())
            ->method('getState')
            ->with('foo');

        $transition = $this->transitionFactory->createFromConfig($config, $this->stateListMock);

        assertSame($this->stateMock, $transition->getTargetState());
    }

    public function testGetsEachSourceStateFromStatelist(): void
    {
        $config = [
            'name' => '',
            'to'   => '',
            'from' => ['foo', 'bar', 'baz'],
        ];

        $this->stateListMock
            ->expects($this->exactly(4)) // first time is for the target state
            ->method('getState')
            ->withConsecutive(
                [''], // <- target state
                ['foo'],
                ['bar'],
                ['baz']
            );

        $transition = $this->transitionFactory->createFromConfig($config, $this->stateListMock);

        assertSame($this->stateMock, $transition->getSourceStates()->getAll()[0]);
    }
}
