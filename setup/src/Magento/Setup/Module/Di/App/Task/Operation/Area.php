<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\App\Task\Operation;

use Magento\Framework\App;
use Magento\Framework\App\ObjectManager\ConfigWriterInterface;
use Magento\Setup\Module\Di\App\Task\OperationInterface;
use Magento\Setup\Module\Di\Compiler\Config;
use Magento\Setup\Module\Di\Compiler\Config\ModificationChain;
use Magento\Setup\Module\Di\Definition\Collection as DefinitionsCollection;

/**
 * Area configuration aggregation.
 */
class Area implements OperationInterface
{
    /**
     * @var App\AreaList
     */
    private $areaList;

    /**
     * @var \Magento\Setup\Module\Di\Code\Reader\Decorator\Area
     */
    private $areaInstancesNamesList;

    /**
     * @var Config\Reader
     */
    private $configReader;

    /**
     * @var ConfigWriterInterface
     */
    private $configWriter;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var ModificationChain
     */
    private $modificationChain;

    /**
     * @param App\AreaList $areaList
     * @param \Magento\Setup\Module\Di\Code\Reader\Decorator\Area $areaInstancesNamesList
     * @param Config\Reader $configReader
     * @param ConfigWriterInterface $configWriter
     * @param ModificationChain $modificationChain
     * @param array $data
     */
    public function __construct(
        App\AreaList $areaList,
        \Magento\Setup\Module\Di\Code\Reader\Decorator\Area $areaInstancesNamesList,
        Config\Reader $configReader,
        ConfigWriterInterface $configWriter,
        ModificationChain $modificationChain,
        $data = [],
    ) {
        $this->areaList = $areaList;
        $this->areaInstancesNamesList = $areaInstancesNamesList;
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
        $this->data = $data;
        $this->modificationChain = $modificationChain;
    }

    /**
     * {@inheritdoc}
     */
    public function doOperation()
    {
        if (empty($this->data)) {
            return;
        }

        $definitionsCollection = new DefinitionsCollection;

        foreach ($this->data as $paths) {
            if (! is_array($paths)) {
                $paths = (array)$paths;
            }

            foreach ($paths as $path) {
                $definitionsCollection->addCollection($this->getDefinitionsCollection($path));
            }
        }

        $this->sortDefinitions($definitionsCollection);

        $areaCodes = array_merge([App\Area::AREA_GLOBAL], $this->areaList->getCodes());

        foreach ($areaCodes as $areaCode) {
            $config = $this->configReader->generateCachePerScope($definitionsCollection, $areaCode);
            $config = $this->modificationChain->modify($config);

            // sort configuration to have it in the same order on every build
            ksort($config['arguments']);
            ksort($config['preferences']);
            ksort($config['instanceTypes']);

            $this->configWriter->write($areaCode, $config);
        }
    }

    /**
     * Returns operation name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Area configuration aggregation';
    }

    /**
     * Returns definitions collection.
     *
     * @param string $path
     *
     * @return DefinitionsCollection
     */
    protected function getDefinitionsCollection($path)
    {
        $definitions = new DefinitionsCollection;

        foreach ($this->areaInstancesNamesList->getList($path) as $className => $constructorArguments) {
            $definitions->addDefinition($className, $constructorArguments);
        }

        return $definitions;
    }

    /**
     * Sort definitions to make reproducible result.
     *
     * @param DefinitionsCollection $definitionsCollection
     */
    private function sortDefinitions(DefinitionsCollection $definitionsCollection): void
    {
        $definitions = $definitionsCollection->getCollection();

        ksort($definitions);

        $definitionsCollection->initialize($definitions);
    }
}
