<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\Config;

use DOMDocument;
use Magento\Framework\Config\ConverterInterface;
use Renttek\StateMachine\Exception\InvalidConfigurationException;
use Renttek\StateMachine\Util\XmlParserUtils;

class Converter implements ConverterInterface
{
    public function __construct(
        private readonly XmlParserUtils $xmlParserUtils,
        private readonly Validator $validator
    ) {
    }

    /**
     * @return array<string, array>
     *
     * @throws InvalidConfigurationException
     *
     * @noinspection PhpMissingParamTypeInspection
     */
    public function convert($source): array
    {
        return [
            'state_machines' => $this->getStateMachines($source),
        ];
    }

    /**
     * @return array<array>
     */
    private function getStateMachines(DOMDocument $domDocument): array
    {
        $stateMachines = $this->xmlParserUtils->getStateMachines($domDocument);

        $this->validator->validate($stateMachines);

        return $stateMachines;
    }
}
