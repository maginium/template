<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency;

use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Setup\Module\Dependency\Circular as CircularTool;
use Magento\Setup\Module\Dependency\Report\BuilderInterface;
use Magento\Setup\Module\Dependency\Report\Circular as CircularReport;
use Magento\Setup\Module\Dependency\Report\Dependency;
use Magento\Setup\Module\Dependency\Report\Framework;

/**
 * Service Locator (instead DI container).
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ServiceLocator
{
    /**
     * Xml config dependencies parser.
     *
     * @var ParserInterface
     */
    private static $xmlConfigParser;

    /**
     * Composer Json parser.
     *
     * @var ParserInterface
     */
    private static $composerJsonParser;

    /**
     * Framework dependencies parser.
     *
     * @var ParserInterface
     */
    private static $frameworkDependenciesParser;

    /**
     * Modules dependencies report builder.
     *
     * @var BuilderInterface
     */
    private static $dependenciesReportBuilder;

    /**
     * Modules circular dependencies report builder.
     *
     * @var BuilderInterface
     */
    private static $circularDependenciesReportBuilder;

    /**
     * Framework dependencies report builder.
     *
     * @var BuilderInterface
     */
    private static $frameworkDependenciesReportBuilder;

    /**
     * Csv file writer.
     *
     * @var Csv
     */
    private static $csvWriter;

    /**
     * Get modules dependencies report builder.
     *
     * @return \Magento\Setup\Module\Dependency\Report\BuilderInterface
     */
    public static function getDependenciesReportBuilder()
    {
        if (self::$dependenciesReportBuilder === null) {
            self::$dependenciesReportBuilder = new Dependency\Builder(
                self::getComposerJsonParser(),
                new Dependency\Writer(self::getCsvWriter()),
            );
        }

        return self::$dependenciesReportBuilder;
    }

    /**
     * Get modules circular dependencies report builder.
     *
     * @return \Magento\Setup\Module\Dependency\Report\BuilderInterface
     */
    public static function getCircularDependenciesReportBuilder()
    {
        if (self::$circularDependenciesReportBuilder === null) {
            self::$circularDependenciesReportBuilder = new CircularReport\Builder(
                self::getComposerJsonParser(),
                new CircularReport\Writer(self::getCsvWriter()),
                new CircularTool,
            );
        }

        return self::$circularDependenciesReportBuilder;
    }

    /**
     * Get framework dependencies report builder.
     *
     * @return \Magento\Setup\Module\Dependency\Report\BuilderInterface
     */
    public static function getFrameworkDependenciesReportBuilder()
    {
        if (self::$frameworkDependenciesReportBuilder === null) {
            self::$frameworkDependenciesReportBuilder = new Framework\Builder(
                self::getFrameworkDependenciesParser(),
                new Framework\Writer(self::getCsvWriter()),
                self::getXmlConfigParser(),
            );
        }

        return self::$frameworkDependenciesReportBuilder;
    }

    /**
     * Get modules dependencies parser.
     *
     * @return \Magento\Setup\Module\Dependency\ParserInterface
     */
    private static function getXmlConfigParser()
    {
        if (self::$xmlConfigParser === null) {
            self::$xmlConfigParser = new Parser\Config\Xml;
        }

        return self::$xmlConfigParser;
    }

    /**
     * Get modules dependencies from composer.json parser.
     *
     * @return \Magento\Setup\Module\Dependency\ParserInterface
     */
    private static function getComposerJsonParser()
    {
        if (self::$composerJsonParser === null) {
            self::$composerJsonParser = new Parser\Composer\Json;
        }

        return self::$composerJsonParser;
    }

    /**
     * Get framework dependencies parser.
     *
     * @return \Magento\Setup\Module\Dependency\ParserInterface
     */
    private static function getFrameworkDependenciesParser()
    {
        if (self::$frameworkDependenciesParser === null) {
            self::$frameworkDependenciesParser = new Parser\Code;
        }

        return self::$frameworkDependenciesParser;
    }

    /**
     * Get csv file writer.
     *
     * @return \Magento\Framework\File\Csv
     */
    private static function getCsvWriter()
    {
        if (self::$csvWriter === null) {
            self::$csvWriter = new Csv(new File);
        }

        return self::$csvWriter;
    }
}
