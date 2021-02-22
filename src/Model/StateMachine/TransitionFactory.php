<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

class TransitionFactory
{
    private LabelFactory $labelFactory;

    public function __construct(LabelFactory $labelFactory)
    {
        $this->labelFactory = $labelFactory;
    }

    /**
     * @param array<string, mixed> $config
     * @param StateList            $stateList
     *
     * @return Transition
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

    public function create(string $name, Label $label, State $targetState, StateList $sourceStates): Transition
    {
        return new Transition(
            $name,
            $label,
            $targetState,
            $sourceStates
        );
    }

    /**
     * @param array<string, mixed> $config
     * @param StateList            $stateList
     *
     * @return State[]
     */
    private function getSourceStates(array $config, StateList $stateList): array
    {
        $states = array_map([$stateList, 'getState'], $config['from']);
        $states = array_filter($states);

        return array_values($states);
    }
}
