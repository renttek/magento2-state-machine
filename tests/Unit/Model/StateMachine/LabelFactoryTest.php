<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\StateMachine\LabelFactory;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class LabelFactoryTest extends TestCase
{
    public function testPassesTextAndTranslateFromConfigToLabel(): void
    {
        $config = [
            'text'      => 'lorem ipsum',
            'translate' => true,
        ];

        $label = (new LabelFactory())->createFromConfig($config);

        assertEquals('lorem ipsum', $label->getText());
        assertTrue($label->isTranslatable());
    }
}
