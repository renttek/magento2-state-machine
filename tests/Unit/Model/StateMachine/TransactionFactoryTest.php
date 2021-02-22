<?php declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\StateMachine\Label;
use Renttek\StateMachine\Model\StateMachine\LabelFactory;
use Renttek\StateMachine\Model\StateMachine\State;
use Renttek\StateMachine\Model\StateMachine\StateList;
use Renttek\StateMachine\Model\StateMachine\TransitionFactory;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

class TransactionFactoryTest extends TestCase
{
    /**
     * @var LabelFactory|MockObject
     */
    private LabelFactory $labelFactoryMock;

    /**
     * @var Label|MockObject
     */
    private Label $labelMock;

    /**
     * @var StateList|MockObject
     */
    private StateList $stateListMock;

    /**
     * @var State|MockObject
     */
    private State $stateMock;

    private TransitionFactory $transactionFactory;

    protected function setUp(): void
    {
        $this->labelFactoryMock = $this->createMock(LabelFactory::class);
        $this->labelMock        = $this->createMock(Label::class);
        $this->stateListMock    = $this->createMock(StateList::class);
        $this->stateMock        = $this->createMock(State::class);

        $this->labelFactoryMock
            ->method('createFromConfig')
            ->willReturn($this->labelMock);

        $this->stateListMock
            ->method('getState')
            ->willReturn($this->stateMock);

        $this->transactionFactory = new TransitionFactory($this->labelFactoryMock);
    }

    public function testPassesNameFromConfigToTransition(): void
    {
        $config = [
            'name' => 'my-transition',
            'to'   => '',
            'from' => [],
        ];

        $transition = $this->transactionFactory->createFromConfig($config, $this->stateListMock);

        assertEquals('my-transition', $transition->getName());
    }

    public function testGetsTargetStateFromStateList(): void
    {
        $config = [
            'name' => '',
            'to'   => 'foo',
            'from' => [],
        ];

        $this->stateListMock
            ->expects(self::once())
            ->method('getState')
            ->with('foo');

        $transition = $this->transactionFactory->createFromConfig($config, $this->stateListMock);

        assertSame($this->stateMock, $transition->getTargetState());
    }

    public function testGetsEachSourceStateFromStatelist(): void
    {
        $config = [
            'name' => '',
            'to'   => '',
            'from' => ['foo', 'bar', 'baz'],
        ];

        $this->stateListMock
            ->expects(self::exactly(4)) // first time is for the target state
            ->method('getState')
            ->withConsecutive(
                ['',], // <- target state
                ['foo'],
                ['bar'],
                ['baz']
            );

        $transition = $this->transactionFactory->createFromConfig($config, $this->stateListMock);

        assertSame($this->stateMock, $transition->getSourceStates()->getAll()[0]);
    }
}
