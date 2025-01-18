<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Parser\Adapter;

use Magento\Setup\Module\I18n\Parser\Adapter\Php\Tokenizer\PhraseCollector;

/**
 * Php parser adapter.
 */
class Php extends AbstractAdapter
{
    /**
     * Phrase collector.
     *
     * @var PhraseCollector
     */
    protected $_phraseCollector;

    /**
     * Adapter construct.
     *
     * @param PhraseCollector $phraseCollector
     */
    public function __construct(PhraseCollector $phraseCollector)
    {
        $this->_phraseCollector = $phraseCollector;
    }

    /**
     * {@inheritdoc}
     */
    protected function _parse()
    {
        $this->_phraseCollector->setIncludeObjects();
        $this->_phraseCollector->parse($this->_file);

        foreach ($this->_phraseCollector->getPhrases() as $phrase) {
            $this->_addPhrase($phrase['phrase'], $phrase['line']);
        }
    }
}
