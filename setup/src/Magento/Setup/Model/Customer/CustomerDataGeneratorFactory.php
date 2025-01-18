<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\Customer;

use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Model\Address\AddressDataGenerator;

/**
 * Create new instance of CustomerDataGenerator.
 */
class CustomerDataGeneratorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create CustomerGenerator instance with specified configuration.
     *
     * @param array $config
     *
     * @return \Magento\Setup\Model\Customer\CustomerDataGenerator
     */
    public function create(array $config)
    {
        return $this->objectManager->create(
            CustomerDataGenerator::class,
            [
                'addressGenerator' => $this->objectManager->create(
                    AddressDataGenerator::class,
                ),
                'config' => $config,
            ],
        );
    }
}
