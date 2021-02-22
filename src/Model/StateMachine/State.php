<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

/**
 * @codeCoverageIgnore
 */
class State
{
    private string $name;
    private Label $label;

    public function __construct(string $name, Label $label)
    {
        $this->name  = $name;
        $this->label = $label;
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
