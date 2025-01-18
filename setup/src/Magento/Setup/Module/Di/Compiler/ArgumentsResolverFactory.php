<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Compiler;

use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManagerInterface;

class ArgumentsResolverFactory
{
    /**
     * Object Manager instance.
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Factory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with config.
     *
     * @param ConfigInterface $diContainerConfig
     *
     * @return \Magento\Setup\Module\Di\Compiler\ArgumentsResolver
     */
    public function create(ConfigInterface $diContainerConfig)
    {
        return new ArgumentsResolver($diContainerConfig);
    }
}
