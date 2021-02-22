<?php declare(strict_types=1);

namespace Renttek\StateMachine\Model\Config;

use DOMDocument;
use Magento\Framework\Config\ConverterInterface;
use Renttek\StateMachine\Exception\InvalidConfigurationException;
use Renttek\StateMachine\Util\XmlParserUtils;

class Converter implements ConverterInterface
{
    private XmlParserUtils $xmlParserUtils;
    private Validator $validator;

    public function __construct(XmlParserUtils $xmlParserUtils, Validator $validator)
    {
        $this->xmlParserUtils = $xmlParserUtils;
        $this->validator      = $validator;
    }

    /**
     * @param DOMDocument $source
     *
     * @return array<string, array>
     *
     * @throws InvalidConfigurationException
     *
     * @noinspection PhpMissingParamTypeInspection
     */
    public function convert($source): array
    {
        if (!$source instanceof DOMDocument) {
            return [];
        }

        return [
            'state_machines' => $this->getStateMachines($source),
        ];
    }

    /**
     * @param DOMDocument $document
     *
     * @return array<array>
     */
    private function getStateMachines(DOMDocument $document): array
    {
        $stateMachines = $this->xmlParserUtils->getStateMachines($document);

        $this->validator->validate($stateMachines);

        return $stateMachines;
    }
}
