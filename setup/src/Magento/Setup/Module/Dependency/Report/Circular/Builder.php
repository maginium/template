<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency\Report\Circular;

use Magento\Setup\Module\Dependency\Circular;
use Magento\Setup\Module\Dependency\ParserInterface;
use Magento\Setup\Module\Dependency\Report\Builder\AbstractBuilder;
use Magento\Setup\Module\Dependency\Report\WriterInterface;

/**
 *  Dependencies report builder.
 */
class Builder extends AbstractBuilder
{
    /**
     * Circular dependencies builder.
     *
     * @var Circular
     */
    protected $circularBuilder;

    /**
     * Builder constructor.
     *
     * @param ParserInterface $dependenciesParser
     * @param WriterInterface $reportWriter
     * @param Circular $circularBuilder
     */
    public function __construct(
        ParserInterface $dependenciesParser,
        WriterInterface $reportWriter,
        Circular $circularBuilder,
    ) {
        parent::__construct($dependenciesParser, $reportWriter);

        $this->circularBuilder = $circularBuilder;
    }

    /**
     * Template method. Prepare data for writer step.
     *
     * @param array $modulesData
     *
     * @return \Magento\Setup\Module\Dependency\Report\Circular\Data\Config
     */
    protected function buildData($modulesData)
    {
        $modules = [];

        foreach ($this->buildCircularDependencies($modulesData) as $moduleName => $modulesChains) {
            $chains = [];

            foreach ($modulesChains as $modulesChain) {
                $chains[] = new Data\Chain($modulesChain);
            }
            $modules[] = new Data\Module($moduleName, $chains);
        }

        return new Data\Config($modules);
    }

    /**
     * Build circular dependencies data by dependencies data.
     *
     * @param array $modulesData
     *
     * @return array
     */
    protected function buildCircularDependencies($modulesData)
    {
        $dependencies = [];

        foreach ($modulesData as $moduleData) {
            foreach ($moduleData['dependencies'] as $dependencyData) {
                $dependencies[$moduleData['name']][] = $dependencyData['module'];
            }
        }

        return $this->circularBuilder->buildCircularDependencies($dependencies);
    }
}
