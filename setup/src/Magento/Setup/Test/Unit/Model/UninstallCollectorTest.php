<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Model\ObjectManagerProvider;
use Magento\Setup\Model\UninstallCollector;
use Magento\Setup\Module\DataSetup;
use Magento\Setup\Module\DataSetupFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UninstallCollectorTest extends TestCase
{
    /**
     * @var UninstallCollector
     */
    private $collector;

    /**
     * @var MockObject|AdapterInterface
     */
    private $adapterInterface;

    /**
     * @var MockObject|Select
     */
    private $result;

    /**
     * @test
     */
    public function uninstallCollector()
    {
        $this->result->expects($this->never())->method('where');
        $this->adapterInterface->expects($this->once())
            ->method('fetchAll')
            ->with($this->result)
            ->willReturn([['module' => 'Magento_A'], ['module' => 'Magento_B'], ['module' => 'Magento_C']]);

        $this->assertEquals(
            ['Magento_A' => 'Uninstall Class A', 'Magento_B' => 'Uninstall Class B'],
            $this->collector->collectUninstall(),
        );
    }

    /**
     * @test
     */
    public function uninstallCollectorWithInput()
    {
        $this->result->expects($this->once())->method('where')->willReturn($this->result);
        $this->adapterInterface->expects($this->once())
            ->method('fetchAll')
            ->with($this->result)
            ->willReturn([['module' => 'Magento_A']]);

        $this->assertEquals(['Magento_A' => 'Uninstall Class A'], $this->collector->collectUninstall(['Magento_A']));
    }

    protected function setUp(): void
    {
        $objectManagerProvider = $this->createMock(ObjectManagerProvider::class);
        $objectManager =
            $this->getMockForAbstractClass(ObjectManagerInterface::class, [], '', false);
        $objectManagerProvider->expects($this->once())->method('get')->willReturn($objectManager);

        $setup = $this->createMock(DataSetup::class);
        $this->adapterInterface = $this->getMockForAbstractClass(
            AdapterInterface::class,
            [],
            '',
            false,
        );
        $select = $this->createPartialMock(Select::class, ['from']);
        $this->adapterInterface->expects($this->once())->method('select')->willReturn($select);
        $setup->expects($this->exactly(2))->method('getConnection')->willReturn($this->adapterInterface);
        $this->result = $this->createMock(Select::class);
        $select->expects($this->once())->method('from')->willReturn($this->result);

        $uninstallA = 'Uninstall Class A';
        $uninstallB = 'Uninstall Class B';
        $objectManager->expects($this->any())
            ->method('create')
            ->willReturnMap(
                [
                    ['Magento\A\Setup\Uninstall', [], $uninstallA],
                    ['Magento\B\Setup\Uninstall', [], $uninstallB],
                ],
            );
        $setupFactory = $this->createMock(DataSetupFactory::class);
        $setupFactory->expects($this->once())->method('create')->willReturn($setup);

        $this->collector = new UninstallCollector($objectManagerProvider, $setupFactory);
    }
}

namespace Magento\Setup\Model;

use Magento\Framework\Setup\UninstallInterface;

/**
 * This function overrides the native function for the purpose of testing.
 *
 * @param string $obj
 * @param string $className
 *
 * @return bool
 */
function is_subclass_of($obj, $className)
{
    if ($obj === 'Uninstall Class A' && $className === UninstallInterface::class) {
        return true;
    }

    return (bool)($obj === 'Uninstall Class B' && $className === UninstallInterface::class);
}

/**
 * This function overrides the native function for the purpose of testing.
 *
 * @param string $className
 *
 * @return bool
 */
function class_exists($className)
{
    return (bool)($className === 'Magento\A\Setup\Uninstall' || $className === 'Magento\B\Setup\Uninstall');
}
