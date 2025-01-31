<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Magento\Framework\Console\Cli;
use Magento\Framework\Setup\ConsoleLogger;
use Magento\Setup\Model\InstallerFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class UninstallCommand extends AbstractSetupCommand
{
    /**
     * @var InstallerFactory
     */
    private $installerFactory;

    /**
     * @param InstallerFactory $installerFactory
     */
    public function __construct(InstallerFactory $installerFactory)
    {
        $this->installerFactory = $installerFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to uninstall Magento?[y/N]', false);

        if ($helper->ask($input, $output, $question) || ! $input->isInteractive()) {
            $installer = $this->installerFactory->create(new ConsoleLogger($output));
            $installer->uninstall();
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('setup:uninstall')
            ->setDescription('Uninstalls the Magento application');
        parent::configure();
    }
}
