<?php declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\StatefulInterface;
use Renttek\StateMachine\Model\StateMachine;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class StateMachineTest extends TestCase
{
    /**
     * @var StateMachine\State|MockObject
     */
    private StateMachine\State $stateMock;

    /**
     * @var StateMachine\StateList|MockObject
     */
    private StateMachine\StateList $stateListMock;

    /**
     * @var StateMachine\TransitionList|MockObject
     */
    private StateMachine\TransitionList $transitionListMock;

    /**
     * @var StateMachine\Transition|MockObject
     */
    private StateMachine\Transition $transitionMock;

    private StatefulInterface $statefulObject;

    private StateMachine $stateMachine;

    protected function setUp(): void
    {
        $this->stateListMock      = $this->createMock(StateMachine\StateList::class);
        $this->transitionListMock = $this->createMock(StateMachine\TransitionList::class);
        $this->transitionMock     = $this->createMock(StateMachine\Transition::class);
        $this->stateMock          = $this->createMock(StateMachine\State::class);

        $this->transitionListMock
            ->method('getTransition')
            ->willReturn($this->transitionMock);

        $this->transitionMock
            ->method('getTargetState')
            ->willReturn($this->stateMock);

        // @formatter:off
        $this->statefulObject = new class implements StatefulInterface {
            private string $state = '';
            public function getState(): string { return $this->state; }
            public function setState(string $state): void { $this->state = $state; }
        };
        // @formatter:on

        $this->stateMachine = new StateMachine(
            clone $this->stateMock,
            $this->stateListMock,
            $this->transitionListMock
        );
    }

    public function testCanFetchesTransitionFromList(): void
    {
        $this->transitionListMock
            ->expects(self::once())
            ->method('getTransition')
            ->with('transition-1');

        $this->stateMachine->can($this->statefulObject, 'transition-1');
    }

    public function testCanChecksTransitionIfItCanBeApplied(): void
    {
        $this->transitionMock
            ->expects(self::once())
            ->method('canApply')
            ->with($this->statefulObject)
            ->willReturn(true);

        assertTrue($this->stateMachine->can($this->statefulObject, ''));
    }

    public function testHasValidStateCheckIfObjectStateExistsInConfiguration(): void
    {
        $state = 'my-state';

        $this->statefulObject->setState($state);
        $this->stateListMock
            ->expects(self::once())
            ->method('has')
            ->with($state)
            ->willReturn(true);

        assertTrue($this->stateMachine->hasValidState($this->statefulObject));
    }

    public function testGetPossibleTransitionsFetchesTransitionsFromList(): void
    {
        $state       = 'my-state';
        $returnValue = [1, 2, 3];

        $this->statefulObject->setState($state);

        $this->transitionListMock
            ->expects(self::once())
            ->method('getTransitionsByState')
            ->with($state)
            ->willReturn($returnValue);

        assertEquals($returnValue, $this->stateMachine->getPossibleTransitions($this->statefulObject));
    }

    public function testApplySetsNameOfFoundTransitionTargetStateToObject(): void
    {
        $state = 'my-state';
        $transitionName = 'transition-1';

        $this->transitionListMock
            ->expects(self::once())
            ->method('getTransition')
            ->with($transitionName);

        $this->transitionMock
            ->expects(self::once())
            ->method('getTargetState');

        $this->stateMock
            ->method('getName')
            ->willReturn($state);

        $this->stateMachine->apply($this->statefulObject, $transitionName);

        assertEquals($state, $this->statefulObject->getState());
    }
}
