<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n;

use const PATHINFO_EXTENSION;

use InvalidArgumentException;

/**
 *  Abstract Factory.
 */
class Factory
{
    /**
     * Create dictionary writer.
     *
     * @param string $filename
     *
     * @throws InvalidArgumentException
     *
     * @return \Magento\Setup\Module\I18n\Dictionary\WriterInterface
     */
    public function createDictionaryWriter($filename = null)
    {
        if (! $filename) {
            $writer = new Dictionary\Writer\Csv\Stdo;
        } else {
            switch (pathinfo($filename, PATHINFO_EXTENSION)) {
                case 'csv':
                default:
                    $writer = new Dictionary\Writer\Csv($filename);

                    break;
            }
        }

        return $writer;
    }

    /**
     * Create locale.
     *
     * @param string $locale
     *
     * @return \Magento\Setup\Module\I18n\Locale
     */
    public function createLocale($locale)
    {
        return new Locale($locale);
    }

    /**
     * Create dictionary.
     *
     * @return \Magento\Setup\Module\I18n\Dictionary
     */
    public function createDictionary()
    {
        return new Dictionary;
    }

    /**
     * Create Phrase.
     *
     * @param array $data
     *
     * @return \Magento\Setup\Module\I18n\Dictionary\Phrase
     */
    public function createPhrase(array $data)
    {
        return new Dictionary\Phrase(
            $data['phrase'],
            $data['translation'],
            $data['context_type'] ?? null,
            $data['context_value'] ?? null,
            $data['quote'] ?? null,
        );
    }
}
