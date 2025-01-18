<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\Quote;

use Magento\Framework\ObjectManagerInterface;

/**
 * Factory class for @see \Magento\Setup\Fixtures\Quote\Configuration.
 */
class QuoteGeneratorFactory
{
    /**
     * Object Manager instance.
     *
     * @var ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * Instance name to create.
     *
     * @var string
     */
    private $instanceName = null;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName [optional]
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = QuoteGenerator::class,
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters.
     *
     * @param array $data [optional]
     *
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
