<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Exception;
use InvalidArgumentException;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\Data\ConfigData;
use Magento\Framework\Setup\FilePermissions;
use Magento\Framework\Setup\Option\AbstractConfigOption;
use Magento\Setup\Exception as SetupException;

class ConfigModel
{
    /**
     * @var ConfigOptionsListCollector
     */
    protected $collector;

    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * File permissions checker.
     *
     * @var FilePermissions
     */
    private $filePermissions;

    /**
     * Constructor.
     *
     * @param ConfigOptionsListCollector $collector
     * @param Writer $writer
     * @param DeploymentConfig $deploymentConfig
     * @param FilePermissions $filePermissions
     */
    public function __construct(
        ConfigOptionsListCollector $collector,
        Writer $writer,
        DeploymentConfig $deploymentConfig,
        FilePermissions $filePermissions,
    ) {
        $this->collector = $collector;
        $this->writer = $writer;
        $this->filePermissions = $filePermissions;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Gets available config options.
     *
     * @return AbstractConfigOption[]
     */
    public function getAvailableOptions()
    {
        /** @var AbstractConfigOption[] $optionCollection */
        $optionCollection = [];
        $optionLists = $this->collector->collectOptionsLists();

        foreach ($optionLists as $optionList) {
            $optionCollection[] = $optionList->getOptions();
        }

        $optionCollection = array_merge([], ...$optionCollection);

        foreach ($optionCollection as $option) {
            $currentValue = $this->deploymentConfig->get($option->getConfigPath());

            if ($currentValue !== null) {
                $option->setDefault();
            }
        }

        return $optionCollection;
    }

    /**
     * Process input options.
     *
     * @param array $inputOptions
     *
     * @throws Exception
     *
     * @return void
     */
    public function process($inputOptions)
    {
        $this->checkInstallationFilePermissions();

        $options = $this->collector->collectOptionsLists();

        foreach ($options as $moduleName => $option) {
            $configData = $option->createConfig($inputOptions, $this->deploymentConfig);

            foreach ($configData as $config) {
                $fileConfigStorage = [];

                if (! $config instanceof ConfigData) {
                    throw new SetupException(
                        'In module : '
                        . $moduleName
                        . 'ConfigOption::createConfig should return an array of ConfigData instances',
                    );
                }

                if (isset($fileConfigStorage[$config->getFileKey()])) {
                    $fileConfigStorage[$config->getFileKey()] = array_replace_recursive(
                        $fileConfigStorage[$config->getFileKey()],
                        $config->getData(),
                    );
                } else {
                    $fileConfigStorage[$config->getFileKey()] = $config->getData();
                }
                $this->writer->saveConfig($fileConfigStorage, $config->isOverrideWhenSave());
            }
        }
    }

    /**
     * Validates Input Options.
     *
     * @param array $inputOptions
     *
     * @return array
     */
    public function validate(array $inputOptions)
    {
        $errors = [];

        //Basic types validation
        $options = $this->getAvailableOptions();

        foreach ($options as $option) {
            try {
                $inputValue = $inputOptions[$option->getName()] ?? null;

                if ($inputValue !== null) {
                    $option->validate($inputValue);
                }
            } catch (InvalidArgumentException $e) {
                $errors[] = [$e->getMessage()];
            }
        }

        // validate ConfigOptionsList
        $options = $this->collector->collectOptionsLists();

        foreach ($options as $option) {
            $errors[] = $option->validate($inputOptions, $this->deploymentConfig);
        }

        return array_merge([], ...$errors);
    }

    /**
     * Check permissions of directories that are expected to be writable for installation.
     *
     * @throws Exception
     *
     * @return void
     */
    private function checkInstallationFilePermissions()
    {
        $results = $this->filePermissions->getMissingWritablePathsForInstallation();

        if ($results) {
            $errorMsg = 'Missing write permissions to the following paths:' . PHP_EOL . implode(PHP_EOL, $results);

            throw new SetupException($errorMsg);
        }
    }
}
