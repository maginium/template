<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Setup\Module\I18n\Dictionary\Generator;

/**
 *  Service Locator (instead DI container).
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ServiceLocator
{
    /**
     * Domain abstract factory.
     *
     * @var Factory
     */
    private static $_factory;

    /**
     * Context manager.
     *
     * @var Factory
     */
    private static $_context;

    /**
     * I18n Dictionary generator.
     *
     * @var Generator
     */
    private static $_dictionaryGenerator;

    /**
     * I18n Pack generator.
     *
     * @var Pack\Generator
     */
    private static $_packGenerator;

    /**
     * Get dictionary generator.
     *
     * @return \Magento\Setup\Module\I18n\Dictionary\Generator
     */
    public static function getDictionaryGenerator()
    {
        if (self::$_dictionaryGenerator === null) {
            $filesCollector = new FilesCollector;

            $phraseCollector = new Parser\Adapter\Php\Tokenizer\PhraseCollector(new Parser\Adapter\Php\Tokenizer);
            $fileSystem = new File;
            $adapters = [
                'php' => new Parser\Adapter\Php($phraseCollector),
                'html' => new Parser\Adapter\Html,
                'js' => new Parser\Adapter\Js($fileSystem),
                'xml' => new Parser\Adapter\Xml,
            ];

            $parser = new Parser\Parser($filesCollector, self::_getFactory());
            $parserContextual = new Parser\Contextual($filesCollector, self::_getFactory(), self::_getContext());

            foreach ($adapters as $type => $adapter) {
                $parser->addAdapter($type, $adapter);
                $parserContextual->addAdapter($type, $adapter);
            }

            self::$_dictionaryGenerator = new Generator(
                $parser,
                $parserContextual,
                self::_getFactory(),
                new Dictionary\Options\ResolverFactory,
            );
        }

        return self::$_dictionaryGenerator;
    }

    /**
     * Get pack generator.
     *
     * @return \Magento\Setup\Module\I18n\Pack\Generator
     */
    public static function getPackGenerator()
    {
        if (self::$_packGenerator === null) {
            $dictionaryLoader = new Dictionary\Loader\File\Csv(self::_getFactory());
            $packWriter = new Pack\Writer\File\Csv(self::_getContext(), $dictionaryLoader, self::_getFactory());

            self::$_packGenerator = new Pack\Generator($dictionaryLoader, $packWriter, self::_getFactory());
        }

        return self::$_packGenerator;
    }

    /**
     * Get factory.
     *
     * @return \Magento\Setup\Module\I18n\Factory
     */
    private static function _getFactory()
    {
        if (self::$_factory === null) {
            self::$_factory = new Factory;
        }

        return self::$_factory;
    }

    /**
     * Get context.
     *
     * @return \Magento\Setup\Module\I18n\Context
     */
    private static function _getContext()
    {
        if (self::$_context === null) {
            self::$_context = new Context(new ComponentRegistrar);
        }

        return self::$_context;
    }
}
