<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model;

use Renttek\StateMachine\Exception\InvalidStateException;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateFactory;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\Transition;
use Renttek\StateMachine\Model\StateMachine\TransitionFactory;
use Renttek\StateMachine\Model\StateMachine\TransitionList;

class StateMachineFactory
{
    /**
     * @var StateMachine[]
     */
    private array $stateMachines;
    private Config $config;
    private StateFactory $stateFactory;
    private TransitionFactory $transitionFactory;

    public function __construct(
        Config $config,
        StateFactory $stateFactory,
        TransitionFactory $transitionFactory
    ) {
        $this->config            = $config;
        $this->stateFactory      = $stateFactory;
        $this->transitionFactory = $transitionFactory;
    }

    public function create(string $name): StateMachine
    {
        $stateMachineConfig = $this->config->getStateMachine($name);

        return $this->createStateMachine($stateMachineConfig);
    }

    public function get(string $name): StateMachine
    {
        if (!isset($this->stateMachines[$name])) {
            $this->stateMachines[$name] = $this->create($name);
        }

        return $this->stateMachines[$name];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return StateMachine
     *
     * @throws InvalidStateException
     */
    private function createStateMachine(array $config): StateMachine
    {
        $stateList      = new StateList(...$this->getStates($config));
        $transitionList = new TransitionList(...$this->getTransitions($config, $stateList));
        $initialState   = $stateList->getState($config['initial_state']);

        return new StateMachine($initialState, $stateList, $transitionList);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return State[]
     */
    private function getStates(array $config): array
    {
        $states = array_map(
            [$this->stateFactory, 'createFromConfig'],
            $config['states']
        );

        return array_values($states);
    }

    /**
     * @param array<string, mixed> $config
     * @param StateList            $stateList
     *
     * @return Transition[]
     */
    private function getTransitions(array $config, StateList $stateList): array
    {
        $createTransitionFn = function (array $config) use ($stateList) {
            return $this->transitionFactory->createFromConfig($config, $stateList);
        };

        $transitions = array_map($createTransitionFn, $config['transitions']);

        return array_values($transitions);
    }
}
