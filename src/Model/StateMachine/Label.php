<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

/**
 * @codeCoverageIgnore
 */
class Label
{
    public function __construct(
        private readonly string $text,
        private readonly bool $isTranslatable
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }
}
