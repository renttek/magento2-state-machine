<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model;

use Magento\Framework\Config\DataInterface;
use Renttek\StateMachine\Exception\StateMachineNotFoundException;

class Config
{
    public function __construct(
        private readonly DataInterface $data
    ) {
    }

    /**
     * @return array<string, array>
     */
    public function getStateMachines(): array
    {
        return $this->data->get('state_machines');
    }

    /**
     * @return array<string, mixed>
     */
    public function getStateMachine(string $name): array
    {
        $stateMachines = $this->getStateMachines();

        if (! array_key_exists($name, $stateMachines)) {
            $message = sprintf('Could not find state machine with the name %s', $name);
            throw new StateMachineNotFoundException($message);
        }

        return $stateMachines[$name];
    }
}
