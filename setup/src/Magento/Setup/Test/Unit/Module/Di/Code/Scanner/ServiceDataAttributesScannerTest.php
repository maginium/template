<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Code\Scanner;

use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderItemExtension;
use Magento\Sales\Api\Data\OrderItemExtensionInterface;
use Magento\Setup\Module\Di\Code\Scanner\ServiceDataAttributesScanner;
use PHPUnit\Framework\TestCase;

class ServiceDataAttributesScannerTest extends TestCase
{
    /**
     * @var ServiceDataAttributesScanner
     */
    protected $model;

    /**
     * @var string
     */
    protected $testFile;

    /**
     * @test
     */
    public function collectEntities()
    {
        $files = [$this->testFile];
        $expectedResult = [
            OrderExtensionInterface::class,
            OrderExtension::class,
            OrderItemExtensionInterface::class,
            OrderItemExtension::class,
        ];
        $this->assertSame($expectedResult, $this->model->collectEntities($files));
    }

    protected function setUp(): void
    {
        $this->model = new ServiceDataAttributesScanner;
        $this->testFile = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files/extension_attributes.xml');
    }
}
