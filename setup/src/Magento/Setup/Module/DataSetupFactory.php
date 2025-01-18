<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module;

use Magento\Framework\Module\Setup\Context;
use Magento\Setup\Model\ObjectManagerProvider;

/**
 * Factory class to create DataSetup.
 *
 * @api
 */
class DataSetupFactory
{
    /**
     * @var ObjectManagerProvider
     */
    private $objectManagerProvider;

    /**
     * Constructor.
     *
     * @param ObjectManagerProvider $objectManagerProvider
     */
    public function __construct(ObjectManagerProvider $objectManagerProvider)
    {
        $this->objectManagerProvider = $objectManagerProvider;
    }

    /**
     * Creates DataSetup.
     *
     * @return DataSetup
     */
    public function create()
    {
        $objectManager = $this->objectManagerProvider->get();

        return new DataSetup($objectManager->get(Context::class));
    }
}
