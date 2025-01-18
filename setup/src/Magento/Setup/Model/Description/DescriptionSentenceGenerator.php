<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\Description;

use Magento\Setup\Model\Dictionary;

/**
 * Generate random sentence for description based on configuration.
 */
class DescriptionSentenceGenerator
{
    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var array
     */
    private $sentenceConfig;

    /**
     * @param Dictionary $dictionary
     * @param array $sentenceConfig
     */
    public function __construct(
        Dictionary $dictionary,
        array $sentenceConfig,
    ) {
        $this->dictionary = $dictionary;
        $this->sentenceConfig = $sentenceConfig;
    }

    /**
     * Generate sentence for description.
     *
     * @return string
     */
    public function generate()
    {
        // mt_rand() here is not for cryptographic use.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $sentenceWordsCount = mt_rand(
            $this->sentenceConfig['words']['count-min'],
            $this->sentenceConfig['words']['count-max'],
        );
        $sentence = '';

        while ($sentenceWordsCount) {
            $sentence .= $this->dictionary->getRandWord();
            $sentence .= ' ';
            $sentenceWordsCount--;
        }

        return ucfirst(rtrim($sentence)) . '.';
    }
}
