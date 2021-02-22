<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

use Renttek\StateMachine\Model\StatefulInterface;

class Transition
{
    private string $name;
    private Label $label;
    private State $targetState;
    private StateList $sourceStates;

    public function __construct(string $name, Label $label, State $targetState, StateList $sourceStates)
    {
        $this->name         = $name;
        $this->label        = $label;
        $this->sourceStates = $sourceStates;
        $this->targetState  = $targetState;
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
        return $this->sourceStates;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTargetState(): State
    {
        return $this->targetState;
    }

    public function canApply(StatefulInterface $object): bool
    {
        return $this->sourceStates->has($object->getState());
    }
}
