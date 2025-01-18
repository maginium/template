<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Pack;

use Magento\Setup\Module\I18n\Dictionary;
use Magento\Setup\Module\I18n\Locale;

/**
 * Pack writer interface.
 */
interface WriterInterface
{
    /**#@+
     * Save pack modes
     */
    public const MODE_REPLACE = 'replace';

    public const MODE_MERGE = 'merge';

    // #@-

    /**
     * Write dictionary data to language pack.
     *
     * @param Dictionary $dictionary
     * @param string $packPath
     * @param Locale $locale
     * @param string $mode One of const of WriterInterface::MODE_
     *
     * @return void
     *
     * @deprecated 2.1.0 Writing to a specified pack path is not supported after custom vendor directory support.
     * Dictionary data will be written to current Magento codebase.
     */
    public function write(Dictionary $dictionary, $packPath, Locale $locale, $mode);

    /**
     * Write dictionary data to current Magento codebase.
     *
     * @param Dictionary $dictionary
     * @param Locale $locale
     * @param string $mode One of const of WriterInterface::MODE_
     *
     * @return void
     */
    public function writeDictionary(Dictionary $dictionary, Locale $locale, $mode);
}
