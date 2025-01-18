<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config;

use Magento\Setup\Module\Di\Compiler\Config\ModificationChain;
use Magento\Setup\Module\Di\Compiler\Config\ModificationInterface;
use PHPUnit\Framework\TestCase;

class ModificationChainTest extends TestCase
{
    /**
     * @test
     */
    public function constructor()
    {
        $modificationsList = [];
        $modificationsList[] = $this->getMockBuilder(
            ModificationInterface::class,
        )->getMock();
        $modificationsList[] = $this->getMockBuilder(
            ModificationInterface::class,
        )->getMock();

        new ModificationChain($modificationsList);
    }

    /**
     * @test
     */
    public function constructorException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Wrong modifier provided');
        $modificationsList = [];
        $modificationsList[] = $this->getMockBuilder(
            ModificationInterface::class,
        )->getMock();
        $modificationsList[] = $this->getMockBuilder(
            ModificationInterface::class,
        )->getMock();
        $modificationsList[] = 'banana';

        new ModificationChain($modificationsList);
    }

    /**
     * @test
     */
    public function modify()
    {
        $inputArray = [
            'data' => [1, 2, 3],
        ];

        $expectedArray1 = [
            'data' => [1, 2, 3, 1],
        ];

        $expectedArray2 = [
            'data' => [1, 2, 3, 1, 1],
        ];

        $modifier1 = $this->getMockBuilder(ModificationInterface::class)
            ->getMock();
        $modifier2 = $this->getMockBuilder(ModificationInterface::class)
            ->getMock();

        $modificationsList = [$modifier1, $modifier2];

        $modifier1->expects($this->once())
            ->method('modify')
            ->with($inputArray)
            ->willReturn($expectedArray1);

        $modifier2->expects($this->once())
            ->method('modify')
            ->with($expectedArray1)
            ->willReturn($expectedArray2);

        $chain = new ModificationChain($modificationsList);

        $this->assertEquals($expectedArray2, $chain->modify($inputArray));
    }
}
