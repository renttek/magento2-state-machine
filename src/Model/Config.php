<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model;

use Magento\Framework\Config\DataInterface;
use Renttek\StateMachine\Exception\StateMachineNotFoundException;

class Config
{
    private DataInterface $dataStorage;

    public function __construct(DataInterface $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * @return array<string, array>
     */
    public function getStateMachines(): array
    {
        return $this->dataStorage->get('state_machines');
    }

    /**
     * @param string $name
     *
     * @return array<string, mixed>
     */
    public function getStateMachine(string $name): array
    {
        $stateMachines = $this->getStateMachines();

        if (!array_key_exists($name, $stateMachines)) {
            $message = sprintf('Could not find state machine with the name %s', $name);
            throw new StateMachineNotFoundException($message);
        }

        return $stateMachines[$name];
    }
}
