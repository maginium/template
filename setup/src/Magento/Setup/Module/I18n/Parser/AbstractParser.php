<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Parser;

use InvalidArgumentException;
use Magento\Setup\Module\I18n;
use Magento\Setup\Module\I18n\Factory;
use Magento\Setup\Module\I18n\FilesCollector;

/**
 * Abstract parser.
 */
abstract class AbstractParser implements I18n\ParserInterface
{
    /**
     * Files collector.
     *
     * @var FilesCollector
     */
    protected $_filesCollector = [];

    /**
     * Domain abstract factory.
     *
     * @var Factory
     */
    protected $_factory;

    /**
     * Adapters.
     *
     * @var AdapterInterface[]
     */
    protected $_adapters = [];

    /**
     * Parsed phrases.
     *
     * @var array
     */
    protected $_phrases = [];

    /**
     * Parser construct.
     *
     * @param FilesCollector $filesCollector
     * @param Factory $factory
     */
    public function __construct(FilesCollector $filesCollector, Factory $factory)
    {
        $this->_filesCollector = $filesCollector;
        $this->_factory = $factory;
    }

    /**
     * Add parser.
     *
     * @param string $type
     * @param AdapterInterface $adapter
     *
     * @return void
     */
    public function addAdapter($type, AdapterInterface $adapter)
    {
        $this->_adapters[$type] = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $parseOptions)
    {
        $this->_validateOptions($parseOptions);

        foreach ($parseOptions as $typeOptions) {
            $this->_parseByTypeOptions($typeOptions);
        }

        return $this->_phrases;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Parse one type.
     *
     * @param array $options
     *
     * @return void
     */
    abstract protected function _parseByTypeOptions($options);

    /**
     * Validate options.
     *
     * @param array $parseOptions
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function _validateOptions($parseOptions)
    {
        foreach ($parseOptions as $parserOptions) {
            if (empty($parserOptions['type'])) {
                throw new InvalidArgumentException('Missed "type" in parser options.');
            }

            if (! isset($this->_adapters[$parserOptions['type']])) {
                throw new InvalidArgumentException(
                    sprintf('Adapter is not set for type "%s".', $parserOptions['type']),
                );
            }

            if (! isset($parserOptions['paths']) || ! is_array($parserOptions['paths'])) {
                throw new InvalidArgumentException('"paths" in parser options must be array.');
            }
        }
    }

    /**
     * Get files for parsing.
     *
     * @param array $options
     *
     * @return array
     */
    protected function _getFiles($options)
    {
        $fileMask = $options['fileMask'] ?? '';

        return $this->_filesCollector->getFiles($options['paths'], $fileMask);
    }
}
