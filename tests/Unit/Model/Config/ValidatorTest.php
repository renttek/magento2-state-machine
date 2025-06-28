<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\Config;

use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Exception\InvalidConfigurationException;
use Renttek\StateMachine\Model\Config\Validator;
use function PHPUnit\Framework\assertTrue;

class ValidatorTest extends TestCase
{
    public function testThrowsAnExceptionIfInitialStateIsNotInListOfAllStates(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'initial_state' => 'foo',
            'states'        => [],
        ];

        (new Validator())->validate([$config]);
    }

    public function testThrowsAnExceptionIfOIneOrMoreSourceStatesDoNotExist(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'initial_state' => 'foo',
            'states'        => [
                'foo' => '',
                'bar' => '',
            ],
            'transitions'   => [
                'do' => [
                    'name' => '',
                    'from' => ['baz'],
                ],
            ],
        ];

        (new Validator())->validate([$config]);
    }

    public function testThrowsAnExceptionIfTargetStateDoNotExist(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'initial_state' => 'foo',
            'states'        => [
                'foo' => '',
                'bar' => '',
            ],
            'transitions'   => [
                'do' => [
                    'name' => '',
                    'from' => [],
                    'to'   => 'baz',
                ],
            ],
        ];

        (new Validator())->validate([$config]);
    }

    public function testThrowsNoExceptionIfEverythingIsValid(): void
    {
        $config = [
            'initial_state' => 'foo',
            'states'        => [
                'foo' => '',
                'bar' => '',
            ],
            'transitions'   => [
                'do' => [
                    'name' => 'foo-bar',
                    'from' => ['foo'],
                    'to'   => 'bar',
                ],
            ],
        ];

        (new Validator())->validate([$config]);

        assertTrue(true);
    }
}
