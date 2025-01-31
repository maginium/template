<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

/**
 * Class PhpInformation.
 *
 * Provides information and checks about the current and required PHP settings.
 */
class PhpInformation
{
    /**
     * Allowed XDebug nested level.
     */
    public const XDEBUG_NESTED_LEVEL = 200;

    /**
     * List of currently installed extensions.
     *
     * @var array
     */
    protected $current = [];

    /**
     * Returns minimum required XDebug nested level.
     *
     * @return int
     */
    public function getRequiredMinimumXDebugNestedLevel()
    {
        return self::XDEBUG_NESTED_LEVEL;
    }

    /**
     * Retrieve list of currently installed extensions.
     *
     * @return array
     */
    public function getCurrent()
    {
        if (! $this->current) {
            $this->current = array_map(fn($ext) => str_replace(' ', '-', mb_strtolower($ext)), get_loaded_extensions());
        }

        return $this->current;
    }
}
