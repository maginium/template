<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console;

use Exception;
use Laminas\ServiceManager\ServiceManager;
use Magento\Setup\Console\Command\AdminUserCreateCommand;
use Magento\Setup\Console\Command\BackupCommand;
use Magento\Setup\Console\Command\ConfigSetCommand;
use Magento\Setup\Console\Command\DbDataUpgradeCommand;
use Magento\Setup\Console\Command\DbSchemaUpgradeCommand;
use Magento\Setup\Console\Command\DbStatusCommand;
use Magento\Setup\Console\Command\DependenciesShowFrameworkCommand;
use Magento\Setup\Console\Command\DependenciesShowModulesCircularCommand;
use Magento\Setup\Console\Command\DependenciesShowModulesCommand;
use Magento\Setup\Console\Command\DeployStaticContentCommand;
use Magento\Setup\Console\Command\DiCompileCommand;
use Magento\Setup\Console\Command\GenerateFixturesCommand;
use Magento\Setup\Console\Command\I18nCollectPhrasesCommand;
use Magento\Setup\Console\Command\I18nPackCommand;
use Magento\Setup\Console\Command\InfoAdminUriCommand;
use Magento\Setup\Console\Command\InfoBackupsListCommand;
use Magento\Setup\Console\Command\InfoCurrencyListCommand;
use Magento\Setup\Console\Command\InfoLanguageListCommand;
use Magento\Setup\Console\Command\InfoTimezoneListCommand;
use Magento\Setup\Console\Command\InstallCommand;
use Magento\Setup\Console\Command\InstallStoreConfigurationCommand;
use Magento\Setup\Console\Command\ModuleConfigStatusCommand;
use Magento\Setup\Console\Command\ModuleDisableCommand;
use Magento\Setup\Console\Command\ModuleEnableCommand;
use Magento\Setup\Console\Command\ModuleStatusCommand;
use Magento\Setup\Console\Command\ModuleUninstallCommand;
use Magento\Setup\Console\Command\RollbackCommand;
use Magento\Setup\Console\Command\UninstallCommand;
use Magento\Setup\Console\Command\UpgradeCommand;

/**
 * Class CommandList contains predefined list of commands for Setup.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CommandList
{
    /**
     * Service Manager.
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Constructor.
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Gets list of command instances.
     *
     * @throws Exception
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getCommands()
    {
        $commands = [];

        foreach ($this->getCommandsClasses() as $class) {
            if (class_exists($class)) {
                $commands[] = $this->serviceManager->get($class);
            } else {
                // phpcs:ignore Magento2.Exceptions.DirectThrow
                throw new Exception('Class ' . $class . ' does not exist');
            }
        }

        return $commands;
    }

    /**
     * Gets list of setup command classes.
     *
     * @return string[]
     */
    protected function getCommandsClasses()
    {
        return [
            AdminUserCreateCommand::class,
            BackupCommand::class,
            ConfigSetCommand::class,
            DbDataUpgradeCommand::class,
            DbSchemaUpgradeCommand::class,
            DbStatusCommand::class,
            DependenciesShowFrameworkCommand::class,
            DependenciesShowModulesCircularCommand::class,
            DependenciesShowModulesCommand::class,
            DiCompileCommand::class,
            GenerateFixturesCommand::class,
            I18nCollectPhrasesCommand::class,
            I18nPackCommand::class,
            InfoAdminUriCommand::class,
            InfoBackupsListCommand::class,
            InfoCurrencyListCommand::class,
            InfoLanguageListCommand::class,
            InfoTimezoneListCommand::class,
            InstallCommand::class,
            InstallStoreConfigurationCommand::class,
            ModuleEnableCommand::class,
            ModuleDisableCommand::class,
            ModuleStatusCommand::class,
            ModuleUninstallCommand::class,
            ModuleConfigStatusCommand::class,
            RollbackCommand::class,
            UpgradeCommand::class,
            UninstallCommand::class,
            DeployStaticContentCommand::class,
        ];
    }
}
