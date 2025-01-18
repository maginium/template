<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\App\ErrorHandler;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\App\State\CleanupFiles;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManager;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\ModuleList\Loader;
use Magento\Framework\Setup\ConsoleLoggerInterface;
use Magento\Framework\Setup\FilePermissions;
use Magento\Framework\Setup\SampleData\State;
use Magento\Setup\Exception;
use Magento\Setup\Module\ConnectionFactory;
use Magento\Setup\Module\DataSetupFactory;
use Magento\Setup\Module\ResourceFactory;
use Magento\Setup\Module\SetupFactory;
use Magento\Setup\Validator\DbValidator;

/**
 * Factory for \Magento\Setup\Model\Installer.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallerFactory
{
    /**
     * Laminas Framework's service locator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param ResourceFactory $resourceFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        ResourceFactory $resourceFactory,
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->resourceFactory = $resourceFactory;
        // For Setup Wizard we are using our customized error handler
        $handler = new ErrorHandler;
        set_error_handler([$handler, 'handler']);
    }

    /**
     * Factory method for installer object.
     *
     * @param ConsoleLoggerInterface $log
     *
     * @throws Exception
     *
     * @return Installer
     */
    public function create(ConsoleLoggerInterface $log)
    {
        return new Installer(
            $this->serviceLocator->get(FilePermissions::class),
            $this->serviceLocator->get(Writer::class),
            $this->serviceLocator->get(Reader::class),
            $this->serviceLocator->get(DeploymentConfig::class),
            $this->serviceLocator->get(ModuleList::class),
            $this->serviceLocator->get(Loader::class),
            $this->serviceLocator->get(AdminAccountFactory::class),
            $log,
            $this->serviceLocator->get(ConnectionFactory::class),
            $this->serviceLocator->get(MaintenanceMode::class),
            $this->serviceLocator->get(Filesystem::class),
            $this->serviceLocator->get(ObjectManagerProvider::class),
            new Context(
                $this->getResource(),
                $this->serviceLocator->get(TransactionManager::class),
                $this->serviceLocator->get(ObjectRelationProcessor::class),
            ),
            $this->serviceLocator->get(ConfigModel::class),
            $this->serviceLocator->get(CleanupFiles::class),
            $this->serviceLocator->get(DbValidator::class),
            $this->serviceLocator->get(SetupFactory::class),
            $this->serviceLocator->get(DataSetupFactory::class),
            $this->serviceLocator->get(State::class),
            new ComponentRegistrar,
            $this->serviceLocator->get(PhpReadinessCheck::class),
        );
    }

    /**
     * Create Resource Factory.
     *
     * @return resource
     */
    private function getResource()
    {
        $deploymentConfig = $this->serviceLocator->get(DeploymentConfig::class);

        return $this->resourceFactory->create($deploymentConfig);
    }
}
