<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

use Renttek\StateMachine\Model\StatefulInterface;

class Transition
{
    public function __construct(
        private readonly string $name,
        private readonly Label $label,
        private readonly State $targetState,
        private readonly StateList $stateList
    ) {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getLabel(): Label
    {
        return $this->label;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSourceStates(): StateList
    {
        return $this->stateList;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTargetState(): State
    {
        return $this->targetState;
    }

    public function canApply(StatefulInterface $stateful): bool
    {
        return $this->stateList->has($stateful->getState());
    }
}
