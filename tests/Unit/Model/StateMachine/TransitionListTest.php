<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Exception\InvalidTransitionException;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\Transition;
use Renttek\StateMachine\Model\StateMachine\TransitionList;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

class TransitionListTest extends TestCase
{
    public function testGetTransitionThrowsExceptionIfNoTransitionWasFound(): void
    {
        $this->expectException(InvalidTransitionException::class);

        (new TransitionList())->getTransition('world');
    }

    public function testGetTransitionFindsStateByName(): void
    {
        $transition1  = $this->createMock(Transition::class);
        $transition1->method('getName')->willReturn('foo');

        $transition2  = $this->createMock(Transition::class);
        $transition2->method('getName')->willReturn('bar');

        $transition3  = $this->createMock(Transition::class);
        $transition3->method('getName')->willReturn('baz');

        $transitionList = new TransitionList(
            $transition1,
            $transition2,
            $transition3
        );

        assertSame($transition2, $transitionList->getTransition('bar'));
    }

    public function testGetTransitionsByStateFiltersTransitionBySourceStates(): void
    {
        $state = 'foo';

        $stateListMock = $this->createMock(StateList::class);
        $stateListMock
            ->expects($this->once())
            ->method('has')
            ->with('foo');

        $transitionMock = $this->createMock(Transition::class);
        $transitionMock->method('getSourceStates')
            ->willReturn($stateListMock);

        (new TransitionList($transitionMock))->getTransitionsByState($state);
    }

    public function testGetAllReturnsAllTransitions(): void
    {
        $transition1  = $this->createMock(Transition::class);
        $transition2  = $this->createMock(Transition::class);

        assertCount(2, (new TransitionList($transition1, $transition2))->getAll());
    }
}
