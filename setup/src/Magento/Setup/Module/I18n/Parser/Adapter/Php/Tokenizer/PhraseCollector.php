<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Parser\Adapter\Php\Tokenizer;

use Magento\Setup\Module\I18n\Parser\Adapter\Php\Tokenizer;
use SplFileInfo;

/**
 * PhraseCollector.
 */
class PhraseCollector
{
    /**
     * @var Tokenizer
     */
    protected $_tokenizer;

    /**
     * Phrases.
     *
     * @var array
     */
    protected $_phrases = [];

    /**
     * Processed file.
     *
     * @var SplFileInfo
     */
    protected $_file;

    /**
     * Are the Phrase objects are parsed as well.
     *
     * @var bool
     */
    protected $includeObjects = false;

    /**
     * The class name of the phrase object.
     */
    protected $className = 'Phrase';

    /**
     * Construct.
     *
     * @param Tokenizer $tokenizer
     * @param bool $includeObjects
     * @param string $className
     */
    public function __construct(Tokenizer $tokenizer, $includeObjects = false, $className = 'Phrase')
    {
        $this->_tokenizer = $tokenizer;
        $this->includeObjects = $includeObjects;
        $this->className = $className;
    }

    /**
     * Get phrases for given file.
     *
     * @return array
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }

    /**
     * Parse given files for phrase.
     *
     * @param string $file
     *
     * @return void
     */
    public function parse($file)
    {
        $this->_phrases = [];
        $this->_file = $file;
        $this->_tokenizer->parse($file);

        while (! $this->_tokenizer->isEndOfLoop()) {
            $this->_extractPhrases();
        }
    }

    /**
     * @param bool $includeObjects
     *
     * @return $this
     */
    public function setIncludeObjects($includeObjects = true)
    {
        $this->includeObjects = (bool)$includeObjects;

        return $this;
    }

    /**
     * Extract phrases from given tokens. e.g.: __('phrase', ...).
     *
     * @return void
     */
    protected function _extractPhrases()
    {
        if ($firstToken = $this->_tokenizer->getNextRealToken()) {
            if (! $this->extractMethodPhrase($firstToken) && $this->includeObjects) {
                $this->extractObjectPhrase($firstToken);
            }
        }
    }

    /**
     * @param Token $firstToken
     *
     * @return bool
     */
    protected function extractMethodPhrase(Token $firstToken)
    {
        if ($firstToken->isEqualFunction('__')) {
            $secondToken = $this->_tokenizer->getNextRealToken();

            if ($secondToken && $secondToken->isOpenBrace()) {
                $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
                $phrase = $this->_collectPhrase(array_shift($arguments));

                if ($phrase !== null) {
                    $this->_addPhrase($phrase, count($arguments), $this->_file, $firstToken->getLine());

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Token $firstToken
     *
     * @return bool
     */
    protected function extractObjectPhrase(Token $firstToken)
    {
        if ($firstToken->isNew() && $this->_tokenizer->isMatchingClass($this->className)) {
            $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
            $phrase = $this->_collectPhrase(array_shift($arguments));

            if ($phrase !== null) {
                $this->_addPhrase($phrase, count($arguments), $this->_file, $firstToken->getLine());

                return true;
            }
        }

        return false;
    }

    /**
     * Collect all phrase parts into string. Return null if phrase is a variable.
     *
     * @param array $phraseTokens
     *
     * @return string|null
     */
    protected function _collectPhrase($phraseTokens)
    {
        $phrase = [];

        if ($phraseTokens) {
            /** @var Token $phraseToken */
            foreach ($phraseTokens as $phraseToken) {
                if ($phraseToken->isConstantEncapsedString() || $phraseToken->isConcatenateOperator()) {
                    $phrase[] = $phraseToken->getValue();
                }
            }

            if ($phrase) {
                return implode(' ', $phrase);
            }
        }
    }

    /**
     * Add phrase.
     *
     * @param string $phrase
     * @param int $argumentsAmount
     * @param SplFileInfo $file
     * @param int $line
     *
     * @return void
     */
    protected function _addPhrase($phrase, $argumentsAmount, $file, $line)
    {
        $this->_phrases[] = [
            'phrase' => $phrase,
            'arguments' => $argumentsAmount,
            'file' => $file,
            'line' => $line,
        ];
    }
}
