<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

class LabelFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public function createFromConfig(array $config): Label
    {
        return $this->create($config['text'], $config['translate']);
    }

    public function create(string $test, bool $translate): Label
    {
        return new Label($test, $translate);
    }
}
