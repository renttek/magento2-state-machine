<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

class StateFactory
{
    public function __construct(
        private readonly LabelFactory $labelFactory
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public function createFromConfig(array $config): State
    {
        $label = $this->labelFactory->createFromConfig($config);

        return $this->create($config['name'], $label);
    }

    public function create(string $name, Label $label): State
    {
        return new State($name, $label);
    }
}
