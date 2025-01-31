<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Exception;
use InvalidArgumentException;
use Magento\Framework\App\Console\MaintenanceModeEnabler;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\App\State;
use Magento\Framework\Backup\Factory;
use Magento\Framework\Console\Cli;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\BackupRollbackFactory;
use Magento\Setup\Model\ObjectManagerProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to backup code base and user data.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BackupCommand extends AbstractSetupCommand
{
    /**
     * Name of input options.
     */
    public const INPUT_KEY_CODE = 'code';

    public const INPUT_KEY_MEDIA = 'media';

    public const INPUT_KEY_DB = 'db';

    /**
     * Object Manager.
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Factory for BackupRollback.
     *
     * @var BackupRollbackFactory
     */
    private $backupRollbackFactory;

    /**
     * Existing deployment config.
     *
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var MaintenanceModeEnabler
     */
    private $maintenanceModeEnabler;

    /**
     * Constructor.
     *
     * @param ObjectManagerProvider $objectManagerProvider
     * @param MaintenanceMode $maintenanceMode deprecated, use $maintenanceModeEnabler instead
     * @param DeploymentConfig $deploymentConfig
     * @param MaintenanceModeEnabler $maintenanceModeEnabler
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        ObjectManagerProvider $objectManagerProvider,
        MaintenanceMode $maintenanceMode,
        DeploymentConfig $deploymentConfig,
        ?MaintenanceModeEnabler $maintenanceModeEnabler = null,
    ) {
        $this->objectManager = $objectManagerProvider->get();
        $this->backupRollbackFactory = $this->objectManager->get(BackupRollbackFactory::class);
        $this->deploymentConfig = $deploymentConfig;
        $this->maintenanceModeEnabler =
            $maintenanceModeEnabler ?: $this->objectManager->get(MaintenanceModeEnabler::class);
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (! $this->deploymentConfig->isAvailable()
            && ($input->getOption(self::INPUT_KEY_MEDIA) || $input->getOption(self::INPUT_KEY_DB))) {
            $output->writeln('<info>No information is available: the Magento application is not installed.</info>');

            // We need exit code higher than 0 here as an indication
            return Cli::RETURN_FAILURE;
        }
        $returnValue = $this->maintenanceModeEnabler->executeInMaintenanceMode(
            function() use ($input, $output) {
                try {
                    $inputOptionProvided = false;
                    $time = time();
                    $backupHandler = $this->backupRollbackFactory->create($output);

                    if ($input->getOption(self::INPUT_KEY_CODE)) {
                        $backupHandler->codeBackup($time);
                        $inputOptionProvided = true;
                    }

                    if ($input->getOption(self::INPUT_KEY_MEDIA)) {
                        $backupHandler->codeBackup($time, Factory::TYPE_MEDIA);
                        $inputOptionProvided = true;
                    }

                    if ($input->getOption(self::INPUT_KEY_DB)) {
                        $this->setAreaCode();
                        $backupHandler->dbBackup($time);
                        $inputOptionProvided = true;
                    }

                    if (! $inputOptionProvided) {
                        throw new InvalidArgumentException(
                            'Not enough information provided to take backup.',
                        );
                    }

                    return Cli::RETURN_SUCCESS;
                } catch (Exception $e) {
                    $output->writeln('<error>' . $e->getMessage() . '</error>');

                    return Cli::RETURN_FAILURE;
                }
            },
            $output,
            false,
        );

        return $returnValue;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::INPUT_KEY_CODE,
                null,
                InputOption::VALUE_NONE,
                'Take code and configuration files backup (excluding temporary files)',
            ),
            new InputOption(
                self::INPUT_KEY_MEDIA,
                null,
                InputOption::VALUE_NONE,
                'Take media backup',
            ),
            new InputOption(
                self::INPUT_KEY_DB,
                null,
                InputOption::VALUE_NONE,
                'Take complete database backup',
            ),
        ];
        $this->setName('setup:backup')
            ->setDescription('Takes backup of Magento Application code base, media and database')
            ->setDefinition($options);
        parent::configure();
    }

    /**
     * Sets area code to start a session for database backup and rollback.
     *
     * @return void
     */
    private function setAreaCode()
    {
        $areaCode = 'adminhtml';

        /** @var State $appState */
        $appState = $this->objectManager->get(State::class);
        $appState->setAreaCode($areaCode);

        /** @var ConfigLoaderInterface $configLoader */
        $configLoader = $this->objectManager->get(ConfigLoaderInterface::class);
        $this->objectManager->configure($configLoader->load($areaCode));
    }
}
