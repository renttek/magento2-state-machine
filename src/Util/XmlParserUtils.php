<?php declare(strict_types=1);

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
     * @param DOMNodeList<DOMNode> $nodeList
     * @param int                  $nodeType
     *
     * @return DOMNode[]
     */
    public function getNodesByType(DOMNodeList $nodeList, int $nodeType): array
    {
        return array_filter(
            iterator_to_array($nodeList),
            static fn(DOMNode $node) => $node->nodeType === $nodeType
        );
    }

    public function getText(DOMNode $node): string
    {
        return (string)$node->nodeValue;
    }

    public function getNameValue(DOMNode $node): string
    {
        return $this->getText($this->getAttributeOrException($node, 'name'));
    }

    public function getAttribute(DOMNode $node, string $name): ?DOMNode
    {
        if ($node->attributes === null) {
            return null;
        }

        $attribute = $node->attributes->getNamedItem($name);

        if ($node->attributes === null) {
            return null;
        }

        return $attribute;
    }

    public function getAttributeOrException(DOMNode $node, string $name): DOMNode
    {
        $attribute = $this->getAttribute($node, $name);

        if ($attribute === null) {
            throw new RuntimeException(sprintf('Requested attribute %s not found', $name));
        }

        return $attribute;
    }

    public function getTranslateValue(DOMNode $node): bool
    {
        $translate = $this->getAttribute($node, 'translate');

        return $translate instanceof DOMNode
            ? filter_var($this->getText($translate), FILTER_VALIDATE_BOOLEAN)
            : false;
    }

    public function getInitialState(DOMNode $initialStateNode): string
    {
        return $this->getText($initialStateNode);
    }

    /**
     * @param DOMNode $statesNode
     *
     * @return array<string, array>
     */
    public function getStates(DOMNode $statesNode): array
    {
        $stateNodes = $this->getNodesByType($statesNode->childNodes, XML_ELEMENT_NODE);
        $states     = array_map(fn ($node) => $this->getState($node), $stateNodes);

        return array_column($states, null, 'name');
    }

    /**
     * @param DOMNode $stateNode
     *
     * @return array<string, mixed>
     */
    public function getState(DOMNode $stateNode): array
    {
        return [
            'name'      => $this->getNameValue($stateNode),
            'text'      => $this->getText($stateNode),
            'translate' => $this->getTranslateValue($stateNode),
        ];
    }

    /**
     * @param DOMNode $transitionsNode
     *
     * @return array<string, array>
     */
    public function getTransitions(DOMNode $transitionsNode): array
    {
        $transitionNodes = $this->getNodesByType($transitionsNode->childNodes, XML_ELEMENT_NODE);
        $transitions     = array_map(fn ($node) => $this->getTransition($node), $transitionNodes);

        return array_column($transitions, null, 'name');
    }

    /**
     * @param DOMNode $transitionNode
     *
     * @return array<string, mixed>
     */
    public function getTransition(DOMNode $transitionNode): array
    {
        $name       = $this->getNameValue($transitionNode);
        $childNodes = $this->getNodesByType($transitionNode->childNodes, XML_ELEMENT_NODE);
        $transition = ['name' => $name, 'from' => []];

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
     * @param DOMNode $labelNode
     *
     * @return array<string, mixed>
     */
    public function getLabel(DOMNode $labelNode): array
    {
        return [
            'translate' => $this->getTranslateValue($labelNode),
            'text'      => $this->getText($labelNode),
        ];
    }

    /**
     * @param DOMNode $stateMachineNode
     *
     * @return array<string, mixed>
     */
    public function getStateMachine(DOMNode $stateMachineNode): array
    {
        $name         = $this->getNameValue($stateMachineNode);
        $childNodes   = $this->getNodesByType($stateMachineNode->childNodes, XML_ELEMENT_NODE);
        $stateMachine = ['name' => $name];

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
     * @param DOMDocument $document
     *
     * @return array<string, array>
     */
    public function getStateMachines(DOMDocument $document): array
    {
        $stateMachineNodes = $document->getElementsByTagName('state_machine');

        $stateMachineNodes = iterator_to_array($stateMachineNodes);
        $stateMachine      = array_map(fn ($node) => $this->getStateMachine($node), $stateMachineNodes);

        return array_column($stateMachine, null, 'name');
    }
}
