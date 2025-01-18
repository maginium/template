<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Code\Scanner;

use Magento\Framework\App\Cache\TagPlugin;
use Magento\Setup\Module\Di\Code\Scanner\PluginScanner;
use Magento\Store\Model\Action\Plugin;
use PHPUnit\Framework\TestCase;

class PluginScannerTest extends TestCase
{
    /**
     * @var PluginScanner
     */
    private $model;

    /**
     * @var string[]
     */
    private $testFiles;

    /**
     * @test
     */
    public function collectEntities()
    {
        $actual = $this->model->collectEntities($this->testFiles);
        $expected = [TagPlugin::class, Plugin::class];
        $this->assertEquals($expected, $actual);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->model = new PluginScanner;
        $testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
        $this->testFiles = [
            $testDir . '/app/code/Magento/SomeModule/etc/di.xml',
            $testDir . '/app/etc/di/config.xml',
        ];
    }

    protected function tearDown(): void
    {
        unset($this->model);
    }
}
