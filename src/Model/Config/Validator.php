<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\Config;

use Renttek\StateMachine\Exception\InvalidConfigurationException;

class Validator
{
    /**
     * @param array<string, mixed> $stateMachines
     *
     * @throws InvalidConfigurationException
     */
    public function validate(array $stateMachines): void
    {
        foreach ($stateMachines as $stateMachine) {
            $this->assertValidInitialState($stateMachine);

            foreach ($stateMachine['transitions'] as $transition) {
                $this->assertTransitionSourceExists($transition, $stateMachine);
                $this->assertTransitionTargetExists($transition, $stateMachine);
            }
        }
    }

    /**
     * @param array<string, mixed> $stateMachine
     *
     * @throws InvalidConfigurationException
     */
    private function assertValidInitialState(array $stateMachine): void
    {
        $initialState = $stateMachine['initial_state'];

        if (!$this->stateExists($initialState, $stateMachine)) {
            $message = sprintf("Invalid initial state: state '%s' does not exist", $initialState);
            throw new InvalidConfigurationException($message);
        }
    }

    /**
     * @param array<string, mixed> $transition
     * @param array<string, mixed> $stateMachine
     *
     * @throws InvalidConfigurationException
     */
    private function assertTransitionTargetExists(array $transition, array $stateMachine): void
    {
        $name   = $transition['name'];
        $target = $transition['to'];

        if (!$this->stateExists($target, $stateMachine)) {
            $message = sprintf("Invalid transition '%s': target state '%s' does not exist", $name, $target);
            throw new InvalidConfigurationException($message);
        }
    }

    /**
     * @param array<string, mixed> $transition
     * @param array<string, mixed> $stateMachine
     *
     * @throws InvalidConfigurationException
     */
    private function assertTransitionSourceExists(array $transition, array $stateMachine): void
    {
        $name    = $transition['name'];
        $sources = $transition['from'];

        foreach ($sources as $source) {
            if (!$this->stateExists($source, $stateMachine)) {
                $message = sprintf("Invalid transition '%s': source state '%s' does not exist", $name, $source);
                throw new InvalidConfigurationException($message);
            }
        }
    }

    /**
     * @param string               $state
     * @param array<string, mixed> $stateMachine
     *
     * @return bool
     */
    private function stateExists(string $state, array $stateMachine): bool
    {
        $states = array_keys($stateMachine['states']);

        return in_array($state, $states, true);
    }
}
