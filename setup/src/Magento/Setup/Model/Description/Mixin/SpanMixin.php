<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\Description\Mixin;

use Magento\Setup\Model\Description\Mixin\Helper\RandomWordSelector;
use Magento\Setup\Model\Description\Mixin\Helper\WordWrapper;

/**
 * Add span html tag to description.
 */
class SpanMixin implements DescriptionMixinInterface
{
    /**
     * @var RandomWordSelector
     */
    private $randomWordSelector;

    /**
     * @var WordWrapper
     */
    private $wordWrapper;

    /**
     * @param RandomWordSelector $randomWordSelector
     * @param WordWrapper $wordWrapper
     */
    public function __construct(
        RandomWordSelector $randomWordSelector,
        WordWrapper $wordWrapper,
    ) {
        $this->randomWordSelector = $randomWordSelector;
        $this->wordWrapper = $wordWrapper;
    }

    /**
     * Add <span></span> tag to text at random positions.
     *
     * @param string $text
     *
     * @return string
     */
    public function apply($text)
    {
        if (empty(strip_tags(trim($text)))) {
            return $text;
        }

        $rawText = strip_tags($text);

        return $this->wordWrapper->wrapWords(
            $text,
            // mt_rand() here is not for cryptographic use.
            // phpcs:ignore Magento2.Security.InsecureFunction
            $this->randomWordSelector->getRandomWords($rawText, mt_rand(5, 8)),
            '<span>%s</span>',
        );
    }
}
