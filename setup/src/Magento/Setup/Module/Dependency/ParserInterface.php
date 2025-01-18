<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency;

/**
 * Parser Interface.
 */
interface ParserInterface
{
    /**
     * Parse files.
     *
     * @param array $options
     *
     * @return array
     */
    public function parse(array $options);
}
