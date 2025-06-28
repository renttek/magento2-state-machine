<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model;

use Renttek\StateMachine\Exception\InvalidTransitionException;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\Transition;
use Renttek\StateMachine\Model\StateMachine\TransitionList;

class StateMachine
{
    public function __construct(
        private readonly State $initialState,
        private readonly StateList $stateList,
        private readonly TransitionList $transitionList
    ) {
    }

    public function can(StatefulInterface $stateful, string $transition): bool
    {
        return $this->transitionList
            ->getTransition($transition)
            ->canApply($stateful);
    }

    /**
     * @throws InvalidTransitionException
     */
    public function apply(StatefulInterface $stateful, string $transition): void
    {
        $targetState = $this->transitionList
            ->getTransition($transition)
            ->getTargetState()
            ->getName();

        $stateful->setState($targetState);
    }

    /**
     * @return Transition[]
     */
    public function getPossibleTransitions(StatefulInterface $stateful): array
    {
        return $this->transitionList->getTransitionsByState($stateful->getState());
    }

    public function hasValidState(StatefulInterface $stateful): bool
    {
        return $this->stateList->has($stateful->getState());
    }

    /**
     * @codeCoverageIgnore
     */
    public function getInitialState(): State
    {
        return $this->initialState;
    }
}
