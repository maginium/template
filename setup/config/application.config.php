<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Deploy\Console\InputValidator;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\App\State;
use Magento\Indexer\Console\Command\IndexerReindexCommand;
use Magento\Setup\Di\MagentoDiFactory;
use Magento\Setup\Model\ConfigGenerator;
use Magento\Setup\Mvc\Bootstrap\InitParamListener;
use Symfony\Component\Console\Helper\TableFactory;

return [
    'modules' => require __DIR__ . '/modules.config.php',
    'module_listener_options' => [
        'module_paths' => [
            __DIR__ . '/../src',
        ],
        'config_glob_paths' => [
            __DIR__ . '/autoload/{,*.}{global,local}.php',
        ],
    ],
    'listeners' => [
        InitParamListener::class,
    ],
    'service_manager' => [
        'factories' => [
            InitParamListener::BOOTSTRAP_PARAM => InitParamListener::class,
            MaintenanceMode::class => MagentoDiFactory::class,
            ConfigGenerator::class => MagentoDiFactory::class,
            IndexerReindexCommand::class => MagentoDiFactory::class,
            TableFactory::class => MagentoDiFactory::class,
            InputValidator::class => MagentoDiFactory::class,
            State::class => MagentoDiFactory::class,
        ],
    ],
];
