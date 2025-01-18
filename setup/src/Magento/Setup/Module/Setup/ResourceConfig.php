<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResourceConnection\ConfigInterface;

/**
 * Simplified resource config for Setup tools.
 */
class ResourceConfig implements ConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConnectionName($resourceName)
    {
        return ResourceConnection::DEFAULT_CONNECTION;
    }
}
