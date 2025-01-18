<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\Description;

/**
 * Generate random paragraph for description based on configuration.
 */
class DescriptionParagraphGenerator
{
    /**
     * @var DescriptionSentenceGenerator
     */
    private $sentenceGenerator;

    /**
     * @var array
     */
    private $paragraphConfig;

    /**
     * @param DescriptionSentenceGenerator $sentenceGenerator
     * @param array $paragraphConfig
     */
    public function __construct(
        DescriptionSentenceGenerator $sentenceGenerator,
        array $paragraphConfig,
    ) {
        $this->sentenceGenerator = $sentenceGenerator;
        $this->paragraphConfig = $paragraphConfig;
    }

    /**
     * Generate paragraph for description.
     *
     * @return string
     */
    public function generate()
    {
        // mt_rand() here is not for cryptographic use.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $sentencesCount = mt_rand(
            $this->paragraphConfig['sentences']['count-min'],
            $this->paragraphConfig['sentences']['count-max'],
        );
        $sentences = '';

        while ($sentencesCount) {
            $sentences .= $this->sentenceGenerator->generate();
            $sentences .= ' ';
            $sentencesCount--;
        }

        $sentences = rtrim($sentences);

        return $sentences;
    }
}
