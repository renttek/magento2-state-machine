<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Exception\InvalidStateException;
use Renttek\StateMachine\Model\StateMachine\Label;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateList;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class StateListTest extends TestCase
{
    public function testGetAllReturnsAllStates(): void
    {
        $label  = $this->createMock(Label::class);
        $state1 = new State('', $label);
        $state2 = new State('', $label);

        assertCount(2, (new StateList($state1, $state2))->getAll());
    }

    public function testGetStateFindsStateByName(): void
    {
        $label  = $this->createMock(Label::class);
        $state1 = new State('hello', $label);
        $state2 = new State('world', $label);
        $state3 = new State('foo', $label);

        /** @var State[] $returnValue */
        $returnValue = (new StateList($state1, $state2, $state3))->getState('world');

        assertSame($state2, $returnValue);
    }

    public function testGetStateThrowsExceptionIfNoStateWasFound(): void
    {
        $this->expectException(InvalidStateException::class);

        (new StateList())->getState('world');
    }

    public function testHasReturnsTrueIfStateWasFound(): void
    {
        $label  = $this->createMock(Label::class);
        $state  = new State('hello', $label);

        assertTrue((new StateList($state))->has('hello'));
    }

    public function testHasReturnsFalseIfStateWasNotFound(): void
    {
        $label  = $this->createMock(Label::class);
        $state  = new State('hello', $label);

        assertFalse((new StateList($state))->has('world'));
    }
}
