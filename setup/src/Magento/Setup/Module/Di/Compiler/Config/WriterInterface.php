<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Compiler\Config;

use Magento\Framework\App\ObjectManager\ConfigWriterInterface;

/**
 * Interface \Magento\Setup\Module\Di\Compiler\Config\WriterInterface.
 *
 * @deprecated Moved to Framework to allow broader reuse
 * @see ConfigWriterInterface
 */
interface WriterInterface
{
    /**
     * Writes config in storage.
     *
     * @param string $key
     * @param array $config
     *
     * @return void
     */
    public function write($key, array $config);
}
