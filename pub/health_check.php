<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*
 * phpcs:disable PSR1.Files.SideEffects
 * phpcs:disable Squiz.Functions.GlobalFunction
 */
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Cache\Frontend\Factory;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

// phpcs:ignore Magento2.Functions.DiscouragedFunction
register_shutdown_function('fatalErrorHandler');

try {
    // phpcs:ignore Magento2.Security.IncludeFile
    require __DIR__ . '/../app/bootstrap.php';

    /** @var ObjectManagerFactory $objectManagerFactory */
    $objectManagerFactory = Bootstrap::createObjectManagerFactory(BP, []);

    /** @var ObjectManagerInterface $objectManager */
    $objectManager = $objectManagerFactory->create([]);

    /** @var DeploymentConfig $deploymentConfig */
    $deploymentConfig = $objectManager->get(DeploymentConfig::class);

    /** @var LoggerInterface $logger */
    $logger = $objectManager->get(LoggerInterface::class);
} catch (\Exception $e) {
    http_response_code(500);

    // phpcs:ignore Magento2.Security.LanguageConstruct
    exit(1);
}

// check mysql connectivity
foreach ($deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS) as $connectionData) {
    try {
        /** @var Mysql $dbAdapter */
        $dbAdapter = $objectManager->create(
            Mysql::class,
            ['config' => $connectionData],
        );
        $dbAdapter->getConnection();
    } catch (\Exception $e) {
        http_response_code(500);
        $logger->error('MySQL connection failed: ' . $e->getMessage());

        // phpcs:ignore Magento2.Security.LanguageConstruct
        exit(1);
    }
}

// check cache storage availability
$cacheConfigs = $deploymentConfig->get(ConfigOptionsListConstants::KEY_CACHE_FRONTEND);

if ($cacheConfigs) {
    foreach ($cacheConfigs as $cacheConfig) {
        // allow config if only available "id_prefix"
        if (count($cacheConfig) === 1 && isset($cacheConfig['id_prefix'])) {
            continue;
        }

        if (! isset($cacheConfig[ConfigOptionsListConstants::CONFIG_PATH_BACKEND]) ||
            ! isset($cacheConfig[ConfigOptionsListConstants::CONFIG_PATH_BACKEND_OPTIONS])) {
            http_response_code(500);
            $logger->error('Cache configuration is invalid');

            // phpcs:ignore Magento2.Security.LanguageConstruct
            exit(1);
        }
        $cacheBackendClass = $cacheConfig[ConfigOptionsListConstants::CONFIG_PATH_BACKEND];

        try {
            /** @var Factory $cacheFrontendFactory */
            $cacheFrontendFactory = $objectManager->get(Factory::class);

            /** @var \Zend_Cache_Backend_Interface $backend */
            $backend = $cacheFrontendFactory->create($cacheConfig);
            $backend->test('test_cache_id');
        } catch (\Exception $e) {
            http_response_code(500);
            $logger->error('Cache storage is not accessible');

            // phpcs:ignore Magento2.Security.LanguageConstruct
            exit(1);
        }
    }
}

/**
 * Handle any fatal errors.
 *
 * @return void
 */
function fatalErrorHandler()
{
    $error = error_get_last();

    if ($error !== null && $error['type'] === E_ERROR) {
        http_response_code(500);
    }
}
