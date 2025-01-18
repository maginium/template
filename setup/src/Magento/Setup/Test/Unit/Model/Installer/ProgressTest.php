<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model\Installer;

use Magento\Setup\Model\Installer\Progress;
use PHPUnit\Framework\TestCase;

class ProgressTest extends TestCase
{
    /**
     * @param int $total
     * @param int $current
     *
     * @dataProvider constructorExceptionInvalidTotalDataProvider
     *
     * @test
     */
    public function constructorExceptionInvalidTotal($total, $current)
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Total number must be more than zero.');
        new Progress($total, $current);
    }

    /**
     * return array.
     */
    public function constructorExceptionInvalidTotalDataProvider()
    {
        return [[0, 0], [0, 1], [[], 1]];
    }

    /**
     * @test
     */
    public function constructorExceptionCurrentExceedsTotal()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Current cannot exceed total number.');
        new Progress(1, 2);
    }

    /**
     * @test
     */
    public function setNext()
    {
        $progress = new Progress(10);
        $progress->setNext();
        $this->assertEquals(1, $progress->getCurrent());
    }

    /**
     * @test
     */
    public function setNextException()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Current cannot exceed total number.');
        $progress = new Progress(10, 10);
        $progress->setNext();
    }

    /**
     * @test
     */
    public function finish()
    {
        $progress = new Progress(10);
        $progress->finish();
        $this->assertEquals(10, $progress->getCurrent());
    }

    /**
     * @test
     */
    public function getCurrent()
    {
        $progress = new Progress(10, 5);
        $this->assertEquals(5, $progress->getCurrent());
    }

    /**
     * @test
     */
    public function getTotal()
    {
        $progress = new Progress(10);
        $this->assertEquals(10, $progress->getTotal());
    }

    /**
     * @param int $total
     * @param int $current
     *
     * @dataProvider ratioDataProvider
     *
     * @test
     */
    public function ratio($total, $current)
    {
        $progress = new Progress($total, $current);
        $this->assertEquals($current / $total, $progress->getRatio());
    }

    /**
     * @return array
     */
    public function ratioDataProvider()
    {
        $data = [];

        for ($i = 10; $i <= 20; $i++) {
            for ($j = 0; $j <= $i; $j++) {
                $data[] = [$i, $j];
            }
        }

        return $data;
    }
}
