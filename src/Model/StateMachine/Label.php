<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\StateMachine;

/**
 * @codeCoverageIgnore
 */
class Label
{
    private string $text;
    private bool $isTranslatable;

    public function __construct(string $text, bool $isTranslatable)
    {
        $this->text           = $text;
        $this->isTranslatable = $isTranslatable;
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
