<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Reader;

/**
 * Interface ClassesScannerInterface.
 */
interface ClassesScannerInterface
{
    /**
     * Retrieves list of classes for given path.
     *
     * @param string $path path to dir with files
     *
     * @return array
     */
    public function getList($path);
}
