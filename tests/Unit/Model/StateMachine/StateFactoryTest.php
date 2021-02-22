<?php declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\StateMachine;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\StateMachine\Label;
use Renttek\StateMachine\Model\StateMachine\LabelFactory;
use Renttek\StateMachine\Model\StateMachine\StateFactory;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

class StateFactoryTest extends TestCase
{
    /**
     * @var LabelFactory|MockObject
     */
    private LabelFactory $labelFactoryMock;

    /**
     * @var Label|MockObject
     */
    private Label $labelMock;

    private StateFactory $stateFactory;

    protected function setUp(): void
    {
        $this->labelFactoryMock = $this->createMock(LabelFactory::class);
        $this->labelMock = $this->createMock(Label::class);

        $this->labelFactoryMock
            ->method('createFromConfig')
            ->willReturn($this->labelMock);

        $this->stateFactory     = new StateFactory($this->labelFactoryMock);
    }

    public function testPassesConfigToLabelFactory(): void
    {
        $config = [
            'name' => 'lorem ipsum',
            'foo'  => 'bar',
        ];

        $this->labelFactoryMock
            ->expects(self::once())
            ->method('createFromConfig')
            ->with($config);

        $this->stateFactory->createFromConfig($config);
    }

    public function testPassesNameToState(): void
    {
        $config = ['name' => 'lorem ipsum'];
        $state = $this->stateFactory->createFromConfig($config);

        assertEquals('lorem ipsum', $state->getName());
    }

    public function testCreatesLabelByLabelFactory(): void
    {
        $config = ['name' => 'lorem ipsum'];
        $state = $this->stateFactory->createFromConfig($config);

        assertSame($this->labelMock, $state->getLabel());
    }
}
