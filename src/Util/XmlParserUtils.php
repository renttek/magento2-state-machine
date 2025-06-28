<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Util;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use RuntimeException;

/**
 * @codeCoverageIgnore
 */
class XmlParserUtils
{
    /**
     * @param DOMNodeList<DOMNode> $domNodeList
     *
     * @return DOMNode[]
     */
    public function getNodesByType(DOMNodeList $domNodeList, int $nodeType): array
    {
        return array_filter(
            iterator_to_array($domNodeList),
            static fn (DOMNode $domNode): bool => $domNode->nodeType === $nodeType
        );
    }

    public function getText(DOMNode $domNode): string
    {
        return (string) $domNode->nodeValue;
    }

    public function getNameValue(DOMNode $domNode): string
    {
        return $this->getText($this->getAttributeOrException($domNode, 'name'));
    }

    public function getAttribute(DOMNode $domNode, string $name): ?DOMNode
    {
        if (! $domNode->attributes instanceof \DOMNamedNodeMap) {
            return null;
        }

        $attribute = $domNode->attributes->getNamedItem($name);

        // @phpstan-ignore-next-line
        if (! $domNode->attributes instanceof \DOMNamedNodeMap) {
            return null;
        }

        return $attribute;
    }

    public function getAttributeOrException(DOMNode $domNode, string $name): DOMNode
    {
        $attribute = $this->getAttribute($domNode, $name);

        if (! $attribute instanceof \DOMNode) {
            throw new RuntimeException(sprintf('Requested attribute %s not found', $name));
        }

        return $attribute;
    }

    public function getTranslateValue(DOMNode $domNode): bool
    {
        $translate = $this->getAttribute($domNode, 'translate');

        return $translate instanceof DOMNode && filter_var($this->getText($translate), FILTER_VALIDATE_BOOLEAN);
    }

    public function getInitialState(DOMNode $domNode): string
    {
        return $this->getText($domNode);
    }

    /**
     * @return array<string, array>
     */
    public function getStates(DOMNode $domNode): array
    {
        $stateNodes = $this->getNodesByType($domNode->childNodes, XML_ELEMENT_NODE);
        $states     = array_map(fn ($node): array => $this->getState($node), $stateNodes);

        return array_column($states, null, 'name');
    }

    /**
     * @return array<string, mixed>
     */
    public function getState(DOMNode $domNode): array
    {
        return [
            'name'      => $this->getNameValue($domNode),
            'text'      => $this->getText($domNode),
            'translate' => $this->getTranslateValue($domNode),
        ];
    }

    /**
     * @return array<string, array>
     */
    public function getTransitions(DOMNode $domNode): array
    {
        $transitionNodes = $this->getNodesByType($domNode->childNodes, XML_ELEMENT_NODE);
        $transitions     = array_map(fn ($node): array => $this->getTransition($node), $transitionNodes);

        return array_column($transitions, null, 'name');
    }

    /**
     * @return array<string, mixed>
     */
    public function getTransition(DOMNode $domNode): array
    {
        $name       = $this->getNameValue($domNode);
        $childNodes = $this->getNodesByType($domNode->childNodes, XML_ELEMENT_NODE);
        $transition = [
            'name' => $name,
            'from' => [],
        ];

        foreach ($childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case 'label':
                    $transition['label'] = $this->getLabel($childNode);
                    break;
                case 'to':
                    $transition['to'] = $this->getNameValue($childNode);
                    break;
                case 'from':
                    $transition['from'][] = $this->getNameValue($childNode);
                    break;
            }
        }

        return $transition;
    }

    /**
     * @return array<string, mixed>
     */
    public function getLabel(DOMNode $domNode): array
    {
        return [
            'translate' => $this->getTranslateValue($domNode),
            'text'      => $this->getText($domNode),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getStateMachine(DOMNode $domNode): array
    {
        $name         = $this->getNameValue($domNode);
        $childNodes   = $this->getNodesByType($domNode->childNodes, XML_ELEMENT_NODE);
        $stateMachine = [
            'name' => $name,
        ];

        foreach ($childNodes as $childNode) {
            switch ($childNode->nodeName) {
                case 'initial_state':
                    $stateMachine['initial_state'] = $this->getInitialState($childNode);
                    break;
                case 'states':
                    $stateMachine['states'] = $this->getStates($childNode);
                    break;
                case 'transitions':
                    $stateMachine['transitions'] = $this->getTransitions($childNode);
                    break;
            }
        }

        return $stateMachine;
    }

    /**
     * @return array<string, array>
     */
    public function getStateMachines(DOMDocument $domDocument): array
    {
        $stateMachineNodes = $domDocument->getElementsByTagName('state_machine');

        $stateMachineNodes = iterator_to_array($stateMachineNodes);

        $stateMachine      = array_map(fn ($node): array => $this->getStateMachine($node), $stateMachineNodes);

        return array_column($stateMachine, null, 'name');
    }
}
