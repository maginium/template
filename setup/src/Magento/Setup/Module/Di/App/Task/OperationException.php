<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\App\Task;

use Exception;

class OperationException extends Exception
{
    /**
     * Unavailable operation code.
     */
    public const UNAVAILABLE_OPERATION = 1;
}
