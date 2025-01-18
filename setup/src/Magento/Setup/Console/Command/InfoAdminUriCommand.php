<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Magento\Backend\Setup\ConfigOptionsList as BackendConfigOptionsList;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoAdminUriCommand extends Command
{
    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * Constructor.
     *
     * @param DeploymentConfig $deploymentConfig
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(DeploymentConfig $deploymentConfig)
    {
        $this->deploymentConfig = $deploymentConfig;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            "\nAdmin URI: /"
            . $this->deploymentConfig->get(BackendConfigOptionsList::CONFIG_PATH_BACKEND_FRONTNAME)
            . "\n",
        );

        return Cli::RETURN_SUCCESS;
    }

    /**
     * Initialization of the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('info:adminuri')
            ->setDescription('Displays the Magento Admin URI');
        parent::configure();
    }
}
