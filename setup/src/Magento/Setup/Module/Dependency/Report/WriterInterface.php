<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency\Report;

use Magento\Setup\Module\Dependency\Report\Data\ConfigInterface;

/**
 *  Writer Interface.
 */
interface WriterInterface
{
    /**
     * Write a report file.
     *
     * @param array $options
     * @param ConfigInterface $config
     *
     * @return void
     */
    public function write(array $options, ConfigInterface $config);
}
