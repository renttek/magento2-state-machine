<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model;

use Renttek\StateMachine\Exception\InvalidTransitionException;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\Transition;
use Renttek\StateMachine\Model\StateMachine\TransitionList;

class StateMachine
{
    private State $initialState;
    private StateList $states;
    private TransitionList $transitions;

    public function __construct(State $initialState, StateList $states, TransitionList $transitions)
    {
        $this->initialState = $initialState;
        $this->states      = $states;
        $this->transitions = $transitions;
    }

    public function can(StatefulInterface $object, string $transition): bool
    {
        return $this->transitions
            ->getTransition($transition)
            ->canApply($object);
    }

    /**
     * @param StatefulInterface $object
     * @param string            $transition
     *
     * @throws InvalidTransitionException
     */
    public function apply(StatefulInterface $object, string $transition): void
    {
        $targetState = $this->transitions
            ->getTransition($transition)
            ->getTargetState()
            ->getName();

        $object->setState($targetState);
    }

    /**
     * @param StatefulInterface $object
     *
     * @return Transition[]
     */
    public function getPossibleTransitions(StatefulInterface $object): array
    {
        return $this->transitions->getTransitionsByState($object->getState());
    }

    public function hasValidState(StatefulInterface $object): bool
    {
        return $this->states->has($object->getState());
    }

    /**
     * @codeCoverageIgnore
     */
    public function getInitialState(): State
    {
        return $this->initialState;
    }
}
