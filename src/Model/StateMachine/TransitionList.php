<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

use Renttek\StateMachine\Exception\InvalidTransitionException;

class TransitionList
{
    /**
     * @var Transition[]
     */
    private array $transitions;

    public function __construct(Transition ...$transitions)
    {
        $this->transitions = $transitions;
    }

    /**
     * @param string $name
     *
     * @return Transition
     *
     * @throws InvalidTransitionException
     */
    public function getTransition(string $name): Transition
    {
        foreach ($this->transitions as $transition) {
            if ($transition->getName() === $name) {
                return $transition;
            }
        }

        throw new InvalidTransitionException(sprintf('Unknown transition %s', $name));
    }

    /**
     * @param string $state
     *
     * @return Transition[]
     */
    public function getTransitionsByState(string $state): array
    {
        return $this->filter(fn (Transition $t)=> $t->getSourceStates()->has($state));
    }

    /**
     * @return Transition[]
     */
    public function getAll(): array
    {
        return $this->transitions;
    }

    /**
     * @param callable $fn
     *
     * @return Transition[]
     */
    private function filter(callable $fn): array
    {
        return array_filter($this->transitions, $fn);
    }
}
