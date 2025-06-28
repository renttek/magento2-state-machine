<?php

declare(strict_types=1);

namespace Renttek\StateMachine\Model\Config;

use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Config\SchemaLocatorInterface;

class SchemaLocator implements SchemaLocatorInterface
{
    private readonly string $schema;

    public function __construct(UrnResolver $urnResolver)
    {
        $this->schema = $urnResolver->getRealPath('urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPerFileSchema(): string
    {
        return $this->schema;
    }
}
