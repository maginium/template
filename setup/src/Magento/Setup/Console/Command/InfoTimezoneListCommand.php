<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Console\Cli;
use Magento\Framework\Setup\Lists;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command prints list of available timezones.
 */
class InfoTimezoneListCommand extends Command
{
    /**
     * List model provides lists of available options for currency, language locales, timezones.
     *
     * @var Lists
     */
    private $lists;

    /**
     * @var TableFactory
     */
    private $tableHelperFactory;

    /**
     * @param Lists $lists
     * @param TableFactory $tableHelperFactory
     */
    public function __construct(Lists $lists, ?TableFactory $tableHelperFactory = null)
    {
        $this->lists = $lists;
        $this->tableHelperFactory = $tableHelperFactory ?: ObjectManager::getInstance()->create(TableFactory::class);
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tableHelper = $this->tableHelperFactory->create(['output' => $output]);
        $tableHelper->setHeaders(['Timezone', 'Code']);

        foreach ($this->lists->getTimezoneList() as $key => $timezone) {
            $tableHelper->addRow([$timezone, $key]);
        }

        $tableHelper->render();

        return Cli::RETURN_SUCCESS;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('info:timezone:list')
            ->setDescription('Displays the list of available timezones');

        parent::configure();
    }
}
