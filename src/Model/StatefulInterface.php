<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model;

interface StatefulInterface
{
    public function getState(): string;

    public function setState(string $state): void;
}
