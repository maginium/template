<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model\FixtureGenerator;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Setup\Model\FixtureGenerator\SqlCollector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zend_Db_Profiler;
use Zend_Db_Profiler_Query;

/**
 * Collect insert queries for quick entity generation.
 */
class SqlCollectorTest extends TestCase
{
    /**
     * @var SqlCollector
     */
    private $unit;

    /**
     * @var MockObject
     */
    private $resourceConnection;

    /**
     * @test
     */
    public function getEmptySql()
    {
        $connection = $this->getMockBuilder(AdapterInterface::class)
            ->addMethods(['getProfiler'])
            ->getMockForAbstractClass();
        $profiler = $this->getMockBuilder(Zend_Db_Profiler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())->method('getProfiler')->willReturn($profiler);
        $this->resourceConnection->expects($this->once())->method('getConnection')->willReturn($connection);

        $profiler->expects($this->once())->method('getQueryProfiles')->willReturn([]);

        $this->unit->disable();
        $this->assertEquals([], $this->unit->getSql());
    }

    /**
     * @test
     */
    public function getEmptySqlWhenSelectQueryProcessed()
    {
        $connection = $this->getMockBuilder(AdapterInterface::class)
            ->addMethods(['getProfiler'])
            ->getMockForAbstractClass();
        $profiler = $this->getMockBuilder(Zend_Db_Profiler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())->method('getProfiler')->willReturn($profiler);
        $this->resourceConnection->expects($this->once())->method('getConnection')->willReturn($connection);

        $query = $this->getMockBuilder(Zend_Db_Profiler_Query::class)->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->exactly(2))->method('getQueryType')->willReturn(Zend_Db_Profiler::SELECT);
        $profiler->expects($this->once())->method('getQueryProfiles')->willReturn([$query]);

        $this->unit->disable();
        $this->assertEquals([], $this->unit->getSql());
    }

    /**
     * @test
     */
    public function getSql()
    {
        $connection = $this->getMockBuilder(AdapterInterface::class)
            ->addMethods(['getProfiler'])
            ->getMockForAbstractClass();
        $profiler = $this->getMockBuilder(Zend_Db_Profiler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->once())->method('getProfiler')->willReturn($profiler);
        $this->resourceConnection->expects($this->once())->method('getConnection')->willReturn($connection);

        $query = $this->getMockBuilder(Zend_Db_Profiler_Query::class)->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('getQueryType')->willReturn(Zend_Db_Profiler::INSERT);
        $query->expects($this->once())->method('getQuery')->willReturn(
            'INSERT INTO `catalog_product_entity` (id, sku, type, created_at, attribute_set)'
            . ' VALUES (?, ?, ?, \'2013-12-11\', ?), (?, ?, ?, \'2013-12-11\', ?)',
        );
        $query->expects($this->once())->method('getQueryParams')->willReturn([
            4, 'sku_4', 'simple', 4, 5, 'sku_5', 'simple', 12,
        ]);
        $profiler->expects($this->once())->method('getQueryProfiles')->willReturn([$query]);

        $this->unit->disable();
        $this->assertEquals(
            [
                [
                    [
                        [
                            'id' => 4,
                            'sku' => 'sku_4',
                            'type' => 'simple',
                            'created_at' => '2013-12-11',
                            'attribute_set' => 4,
                        ],
                        [
                            'id' => 5,
                            'sku' => 'sku_5',
                            'type' => 'simple',
                            'created_at' => '2013-12-11',
                            'attribute_set' => 12,
                        ],
                    ],
                    'catalog_product_entity',
                ],
            ],
            $this->unit->getSql(),
        );
    }

    protected function setUp(): void
    {
        $this->resourceConnection = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->unit = (new ObjectManager($this))->getObject(
            SqlCollector::class,
            ['resourceConnection' => $this->resourceConnection],
        );
    }
}
