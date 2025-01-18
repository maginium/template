<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

/**
 * Generate description for product.
 */
interface DescriptionGeneratorInterface
{
    /**
     * Generate description per product net.
     *
     * @param int $entityIndex
     *
     * @return string
     */
    public function generate($entityIndex);
}
