<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Setup\Module\DataSetup;
use Magento\Setup\Module\DataSetupFactory;

/**
 * Class for collecting all Uninstall interfaces in all modules.
 */
class UninstallCollector
{
    /**
     * Object manager.
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * DataSetup Factory.
     *
     * @var DataSetupFactory
     */
    private $dataSetupFactory;

    /**
     * Constructor.
     *
     * @param ObjectManagerProvider $objectManagerProvider
     * @param DataSetupFactory $dataSetupFactory
     */
    public function __construct(
        ObjectManagerProvider $objectManagerProvider,
        DataSetupFactory $dataSetupFactory,
    ) {
        $this->objectManager = $objectManagerProvider->get();
        $this->dataSetupFactory = $dataSetupFactory;
    }

    /**
     * Collect Uninstall classes from modules.
     *
     * @param array $filterModules
     *
     * @return UninstallInterface[]
     */
    public function collectUninstall($filterModules = [])
    {
        $uninstallList = [];

        /** @var DataSetup $setup */
        $setup = $this->dataSetupFactory->create();
        $result = $setup->getConnection()->select()->from($setup->getTable('setup_module'), ['module']);

        if (isset($filterModules) && count($filterModules) > 0) {
            $result->where('module in( ? )', $filterModules);
        }

        // go through modules
        foreach ($setup->getConnection()->fetchAll($result) as $row) {
            $uninstallClassName = str_replace('_', '\\', $row['module']) . '\Setup\Uninstall';

            if (class_exists($uninstallClassName)) {
                $uninstallClass = $this->objectManager->create($uninstallClassName);

                if (is_subclass_of($uninstallClass, UninstallInterface::class)) {
                    $uninstallList[$row['module']] = $uninstallClass;
                }
            }
        }

        return $uninstallList;
    }
}
