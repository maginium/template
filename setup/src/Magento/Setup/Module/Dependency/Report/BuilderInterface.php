<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency\Report;

/**
 *  Builder Interface.
 */
interface BuilderInterface
{
    /**
     * Build a report.
     *
     * @param array $options
     *
     * @return void
     */
    public function build(array $options);
}
