<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

use Renttek\StateMachine\Exception\InvalidStateException;

class StateList
{
    /**
     * @var State[]
     */
    private array $states;

    public function __construct(State ...$states)
    {
        $this->states = $states;
    }

    public function getState(string $name): State
    {
        foreach ($this->states as $states) {
            if ($states->getName() === $name) {
                return $states;
            }
        }

        throw new InvalidStateException(sprintf('Requested state not found: %s', $name));
    }

    /**
     * @return State[]
     */
    public function getAll(): array
    {
        return $this->states;
    }

    public function has(string $name): bool
    {
        try {
            $this->getState($name);
        } catch (InvalidStateException $invalidStateException) {
            return false;
        }
        return true;
    }
}
