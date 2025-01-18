<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Reader;

use ReflectionClass;
use ReflectionException;

class Type
{
    /**
     * Whether instance is concrete implementation.
     *
     * @param string $type
     *
     * @return bool
     */
    public function isConcrete($type)
    {
        try {
            $instance = new ReflectionClass($type);
        } catch (ReflectionException $e) {
            return false;
        }

        return ! $instance->isAbstract() && ! $instance->isInterface();
    }
}
