<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Dictionary\Loader;

use InvalidArgumentException;

/**
 * Dictionary loader interface.
 */
interface FileInterface
{
    /**
     * Load dictionary.
     *
     * @param string $file
     *
     * @throws InvalidArgumentException
     *
     * @return \Magento\Setup\Module\I18n\Dictionary
     */
    public function load($file);
}
