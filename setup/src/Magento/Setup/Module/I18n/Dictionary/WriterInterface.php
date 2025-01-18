<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Dictionary;

/**
 * Writer interface.
 */
interface WriterInterface
{
    /**
     * Write data to dictionary.
     *
     * @param Phrase $phrase
     *
     * @return void
     */
    public function write(Phrase $phrase);
}
