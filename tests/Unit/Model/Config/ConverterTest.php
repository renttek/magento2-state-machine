<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Tests\Unit\Model\Config;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\StateMachine\Model\Config\Converter;
use Renttek\StateMachine\Model\Config\Validator;
use Renttek\StateMachine\Util\XmlParserUtils;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class ConverterTest extends TestCase
{
    private MockObject&XmlParserUtils $xmlParserMock;
    private MockObject&Validator $validatorMock;
    private Converter $converter;

    protected function setUp(): void
    {
        $this->xmlParserMock = $this->createMock(XmlParserUtils::class);
        $this->validatorMock = $this->createMock(Validator::class);

        $this->converter = new Converter($this->xmlParserMock, $this->validatorMock);
    }

    public function testReadsStateMachinesFromXmlParser(): void
    {
        $documentMock = $this->createMock(\DOMDocument::class);

        $this->xmlParserMock
            ->expects($this->once())
            ->method('getStateMachines')
            ->with($documentMock);

        $this->converter->convert($documentMock);
    }

    public function testPassesReadXmlConfigToValidator(): void
    {
        $config = [
            'foo' => 'bar',
        ];

        $this->xmlParserMock
            ->method('getStateMachines')
            ->willReturn($config);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($config);

        $this->converter->convert($this->createMock(\DOMDocument::class));
    }

    public function testReturnValueOfConvertContainsStateMachineConfig(): void
    {
        $this->xmlParserMock
            ->method('getStateMachines')
            ->willReturn([]);

        $returnValue = $this->converter->convert($this->createMock(\DOMDocument::class));

        assertArrayHasKey('state_machines', $returnValue);
    }

    public function testReturnValueOfConvertContainsStateMachineConfigArray(): void
    {
        $config = [
            'foo' => 'bar',
        ];

        $documentMock = $this->createMock(\DOMDocument::class);

        $this->xmlParserMock
            ->method('getStateMachines')
            ->willReturn($config);

        $returnValue = $this->converter->convert($documentMock);
        assertEquals($config, $returnValue['state_machines']);
    }
}
