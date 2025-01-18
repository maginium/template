<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\App\Task\Operation;

use Magento\Setup\Module\Di\App\Task\OperationInterface;
use Magento\Setup\Module\Di\Code\Scanner;
use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;

class ProxyGenerator implements OperationInterface
{
    /**
     * @var Scanner\XmlScanner
     */
    private $proxyScanner;

    /**
     * @var array
     */
    private $data;

    /**
     * @var ConfigurationScanner
     */
    private $configurationScanner;

    /**
     * @param Scanner\XmlScanner $proxyScanner
     * @param ConfigurationScanner $configurationScanner
     * @param array $data
     */
    public function __construct(
        Scanner\XmlScanner $proxyScanner,
        ConfigurationScanner $configurationScanner,
        $data = [],
    ) {
        $this->proxyScanner = $proxyScanner;
        $this->data = $data;
        $this->configurationScanner = $configurationScanner;
    }

    /**
     * Processes operation task.
     *
     * @return void
     */
    public function doOperation()
    {
        $files = $this->configurationScanner->scan('di.xml');
        $proxies = $this->proxyScanner->collectEntities($files);

        foreach ($proxies as $entityName) {
            class_exists($entityName);
        }
    }

    /**
     * Returns operation name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Proxies code generation';
    }
}
