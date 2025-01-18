<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Parser;

/**
 * Adapter Interface.
 */
interface AdapterInterface
{
    /**
     * Parse file.
     *
     * @param string $file
     *
     * @return array
     */
    public function parse($file);

    /**
     * Get parsed phrases.
     *
     * @return array
     */
    public function getPhrases();
}
