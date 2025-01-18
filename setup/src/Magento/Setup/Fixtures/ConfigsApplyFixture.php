<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures;

use Magento\Config\App\Config\Type\System;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueInterface;

/**
 * Class ConfigsApplyFixture.
 */
class ConfigsApplyFixture extends Fixture
{
    /**
     * @var int
     */
    protected $priority = -1;

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $configs = $this->fixtureModel->getValue('configs', []);

        if (empty($configs)) {
            return;
        }

        foreach ($configs['config'] as $config) {
            $backendModel = $config['backend_model'] ?? Value::class;

            /**
             * @var ValueInterface $configData
             */
            $configData = $this->fixtureModel->getObjectManager()->create($backendModel);
            $configData->setPath($config['path'])
                ->setScope($config['scope'])
                ->setScopeId($config['scopeId'])
                ->setValue($config['value'])
                ->save();
        }
        $this->fixtureModel->getObjectManager()
            ->get(CacheInterface::class)
            ->clean([Config::CACHE_TAG]);

        $this->fixtureModel->getObjectManager()
            ->get(System::class)
            ->clean();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle()
    {
        return 'Config Changes';
    }

    /**
     * {@inheritdoc}
     */
    public function introduceParamLabels()
    {
        return [];
    }
}
