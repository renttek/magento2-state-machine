<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

/**
 * @codeCoverageIgnore
 */
class State
{
    public function __construct(
        private readonly string $name,
        private readonly Label $label
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }
}
