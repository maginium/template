<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\App\Task;

use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Model\ObjectManagerProvider;
use Magento\Setup\Module\Di\App\Task\Operation\AppActionListGenerator;
use Magento\Setup\Module\Di\App\Task\Operation\ApplicationCodeGenerator;
use Magento\Setup\Module\Di\App\Task\Operation\Area;
use Magento\Setup\Module\Di\App\Task\Operation\Interception;
use Magento\Setup\Module\Di\App\Task\Operation\InterceptionCache;
use Magento\Setup\Module\Di\App\Task\Operation\PluginListGenerator;
use Magento\Setup\Module\Di\App\Task\Operation\ProxyGenerator;
use Magento\Setup\Module\Di\App\Task\Operation\RepositoryGenerator;
use Magento\Setup\Module\Di\App\Task\Operation\ServiceDataAttributesGenerator;

/**
 * Factory that creates list of OperationInterface classes.
 */
class OperationFactory
{
    /**
     * Area config generator operation definition.
     */
    public const AREA_CONFIG_GENERATOR = 'area';

    /**
     * Interception operation definition.
     */
    public const INTERCEPTION = 'interception';

    /**
     * Interception cache operation definition.
     */
    public const INTERCEPTION_CACHE = 'interception_cache';

    /**
     * Repository generator operation definition.
     */
    public const REPOSITORY_GENERATOR = 'repository_generator';

    /**
     * Proxy generator operation definition.
     */
    public const PROXY_GENERATOR = 'proxy_generator';

    /**
     * Service data attributes generator operation definition.
     */
    public const DATA_ATTRIBUTES_GENERATOR = 'extension_attributes_generator';

    /**
     * Application code generator operation definition.
     */
    public const APPLICATION_CODE_GENERATOR = 'application_code_generator';

    /**
     * Application action list generator operation definition.
     */
    public const APPLICATION_ACTION_LIST_GENERATOR = 'application_action_list_generator';

    /**
     * Plugin list generator operation definition.
     */
    public const PLUGIN_LIST_GENERATOR = 'plugin_list_generator';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Operations definitions.
     *
     * @var array
     */
    private $operationsDefinitions = [
        self::DATA_ATTRIBUTES_GENERATOR => ServiceDataAttributesGenerator::class,
        self::AREA_CONFIG_GENERATOR => Area::class,
        self::APPLICATION_CODE_GENERATOR => ApplicationCodeGenerator::class,
        self::INTERCEPTION => Interception::class,
        self::INTERCEPTION_CACHE => InterceptionCache::class,
        self::REPOSITORY_GENERATOR => RepositoryGenerator::class,
        self::PROXY_GENERATOR => ProxyGenerator::class,
        self::APPLICATION_ACTION_LIST_GENERATOR => AppActionListGenerator::class,
        self::PLUGIN_LIST_GENERATOR => PluginListGenerator::class,
    ];

    /**
     * @param ObjectManagerProvider $objectManagerProvider
     */
    public function __construct(ObjectManagerProvider $objectManagerProvider)
    {
        $this->objectManager = $objectManagerProvider->get();
    }

    /**
     * Creates operation.
     *
     * @param string $operationAlias
     * @param mixed $arguments
     *
     * @throws OperationException
     *
     * @return OperationInterface
     */
    public function create($operationAlias, $arguments = null)
    {
        if (! array_key_exists($operationAlias, $this->operationsDefinitions)) {
            throw new OperationException(
                sprintf('Unrecognized operation "%s"', $operationAlias),
                OperationException::UNAVAILABLE_OPERATION,
            );
        }

        return $this->objectManager->create($this->operationsDefinitions[$operationAlias], ['data' => $arguments]);
    }
}
