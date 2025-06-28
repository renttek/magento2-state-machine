<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

class TransitionFactory
{
    public function __construct(
        private readonly LabelFactory $labelFactory
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public function createFromConfig(array $config, StateList $stateList): Transition
    {
        $label        = $this->labelFactory->createFromConfig($config);
        $targetState  = $stateList->getState($config['to']);
        $sourceStates = $this->getSourceStates($config, $stateList);

        return $this->create(
            $config['name'],
            $label,
            $targetState,
            new StateList(...$sourceStates)
        );
    }

    public function create(string $name, Label $label, State $targetState, StateList $stateList): Transition
    {
        return new Transition(
            $name,
            $label,
            $targetState,
            $stateList
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return State[]
     */
    private function getSourceStates(array $config, StateList $stateList): array
    {
        $states = array_map($stateList->getState(...), $config['from']);
        $states = array_filter($states);

        return array_values($states);
    }
}
