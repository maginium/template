<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\App\Task\Operation;

use Magento\Framework\Interception\Config\Config;
use Magento\Setup\Module\Di\App\Task\OperationInterface;
use Magento\Setup\Module\Di\Code\Reader\Decorator\Interceptions;

class InterceptionCache implements OperationInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var Config
     */
    private $configInterface;

    /**
     * @var Interceptions
     */
    private $interceptionsInstancesNamesList;

    /**
     * @param Config $configInterface
     * @param Interceptions $interceptionsInstancesNamesList
     * @param array $data
     */
    public function __construct(
        Config $configInterface,
        Interceptions $interceptionsInstancesNamesList,
        array $data = [],
    ) {
        $this->configInterface = $configInterface;
        $this->interceptionsInstancesNamesList = $interceptionsInstancesNamesList;
        $this->data = $data;
    }

    /**
     * Flushes interception cached configuration and generates a new one.
     *
     * @return void
     */
    public function doOperation()
    {
        if (empty($this->data)) {
            return;
        }

        $definitions = [];

        foreach ($this->data as $paths) {
            if (! is_array($paths)) {
                $paths = (array)$paths;
            }

            foreach ($paths as $path) {
                $definitions[] = $this->interceptionsInstancesNamesList->getList($path);
            }
        }

        $definitions = array_merge([], ...$definitions);

        $this->configInterface->initialize($definitions);
    }

    /**
     * Returns operation name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Interception cache generation';
    }
}
