<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Framework\Composer\Remove;
use Magento\Framework\Module\ModuleResource;
use Magento\Framework\Module\PackageInfo;
use Magento\Framework\Module\PackageInfoFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\Patch\PatchApplier;
use Magento\Setup\Module\SetupFactory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class to uninstall a module component.
 */
class ModuleUninstaller
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Remove
     */
    private $remove;

    /**
     * @var UninstallCollector
     */
    private $collector;

    /**
     * @var SetupFactory
     */
    private $setupFactory;

    /**
     * @var PatchApplier
     */
    private $patchApplier;

    /**
     * Constructor.
     *
     * @param ObjectManagerProvider $objectManagerProvider
     * @param Remove $remove
     * @param UninstallCollector $collector
     * @param SetupFactory $setupFactory
     * @param PatchApplier $patchApplier
     */
    public function __construct(
        ObjectManagerProvider $objectManagerProvider,
        Remove $remove,
        UninstallCollector $collector,
        SetupFactory $setupFactory,
    ) {
        $this->objectManager = $objectManagerProvider->get();
        $this->remove = $remove;
        $this->collector = $collector;
        $this->setupFactory = $setupFactory;
    }

    /**
     * Invoke remove data routine in each specified module.
     *
     * @param OutputInterface $output
     * @param array $modules
     *
     * @return void
     */
    public function uninstallData(OutputInterface $output, array $modules)
    {
        $uninstalls = $this->collector->collectUninstall($modules);
        $setupModel = $this->setupFactory->create();
        $resource = $this->objectManager->get(ModuleResource::class);

        foreach ($modules as $module) {
            if (isset($uninstalls[$module])) {
                $output->writeln("<info>Removing data of {$module}</info>");
                $uninstalls[$module]->uninstall(
                    $setupModel,
                    new ModuleContext($resource->getDbVersion($module) ?: ''),
                );
            }

            $this->getPatchApplier()->revertDataPatches($module);
        }
    }

    /**
     * Run 'composer remove' to remove code.
     *
     * @param OutputInterface $output
     * @param array $modules
     *
     * @return void
     */
    public function uninstallCode(OutputInterface $output, array $modules)
    {
        $output->writeln('<info>Removing code from Magento codebase:</info>');
        $packages = [];

        /** @var PackageInfo $packageInfo */
        $packageInfo = $this->objectManager->get(PackageInfoFactory::class)->create();

        foreach ($modules as $module) {
            $packages[] = $packageInfo->getPackageName($module);
        }
        $this->remove->remove($packages);
    }

    /**
     * @return PatchApplier
     */
    private function getPatchApplier()
    {
        if (! $this->patchApplier) {
            $this->patchApplier = $this->objectManager->get(PatchApplier::class);
        }

        return $this->patchApplier;
    }
}
