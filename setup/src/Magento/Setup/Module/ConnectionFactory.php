<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Magento\Framework\DB\Adapter\Pdo\MysqlFactory;
use Magento\Framework\DB\Logger\Quiet;
use Magento\Framework\DB\Platform\Quote;
use Magento\Framework\DB\Select\ColumnsRenderer;
use Magento\Framework\DB\Select\DistinctRenderer;
use Magento\Framework\DB\Select\ForUpdateRenderer;
use Magento\Framework\DB\Select\FromRenderer;
use Magento\Framework\DB\Select\GroupRenderer;
use Magento\Framework\DB\Select\HavingRenderer;
use Magento\Framework\DB\Select\LimitRenderer;
use Magento\Framework\DB\Select\OrderRenderer;
use Magento\Framework\DB\Select\SelectRenderer;
use Magento\Framework\DB\Select\UnionRenderer;
use Magento\Framework\DB\Select\WhereRenderer;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactoryInterface;
use Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Mysql;
use Magento\Setup\Model\ObjectManagerProvider;

/**
 * Connection adapter factory.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $connectionConfig)
    {
        $quote = new Quote;
        $selectFactory = new SelectFactory(
            new SelectRenderer(
                [
                    'distinct' => [
                        'renderer' => new DistinctRenderer,
                        'sort' => 100,
                        'part' => 'distinct',
                    ],
                    'columns' => [
                        'renderer' => new ColumnsRenderer($quote),
                        'sort' => 200,
                        'part' => 'columns',
                    ],
                    'union' => [
                        'renderer' => new UnionRenderer,
                        'sort' => 300,
                        'part' => 'union',
                    ],
                    'from' => [
                        'renderer' => new FromRenderer($quote),
                        'sort' => 400,
                        'part' => 'from',
                    ],
                    'where' => [
                        'renderer' => new WhereRenderer,
                        'sort' => 500,
                        'part' => 'where',
                    ],
                    'group' => [
                        'renderer' => new GroupRenderer($quote),
                        'sort' => 600,
                        'part' => 'group',
                    ],
                    'having' => [
                        'renderer' => new HavingRenderer,
                        'sort' => 700,
                        'part' => 'having',
                    ],
                    'order' => [
                        'renderer' => new OrderRenderer($quote),
                        'sort' => 800,
                        'part' => 'order',
                    ],
                    'limit' => [
                        'renderer' => new LimitRenderer,
                        'sort' => 900,
                        'part' => 'limitcount',
                    ],
                    'for_update' => [
                        'renderer' => new ForUpdateRenderer,
                        'sort' => 1000,
                        'part' => 'forupdate',
                    ],
                ],
            ),
        );
        $objectManagerProvider = $this->serviceLocator->get(ObjectManagerProvider::class);
        $mysqlFactory = new MysqlFactory($objectManagerProvider->get());
        $resourceInstance = new Mysql($connectionConfig, $mysqlFactory);

        return $resourceInstance->getConnection(
            $this->serviceLocator->get(Quiet::class),
            $selectFactory,
        );
    }
}
