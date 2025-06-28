<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\StatefulInterface;
use Renttek\StateMachine\Model\StateMachine\Label;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\Transition;

class TransitionTest extends TestCase
{
    public function testCanApplyFindsPossibleStatesUsingStateList(): void
    {
        $statefulObject = new class() implements StatefulInterface {
            public function getState(): string
            {
                return 'mystate';
            }

            public function setState(string $state): void
            {
            }
        };

        $stateList = $this->createMock(StateList::class);
        $stateList->expects($this->once())
            ->method('has')
            ->with('mystate');

        $transition = new Transition(
            '',
            $this->createMock(Label::class),
            $this->createMock(State::class),
            $stateList
        );

        $transition->canApply($statefulObject);
    }
}
