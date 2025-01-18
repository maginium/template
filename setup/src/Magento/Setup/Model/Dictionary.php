<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Setup\Exception;
use SplFixedArray;

/**
 * Provide random word from dictionary.
 */
class Dictionary
{
    /**
     * @var string
     */
    private $dictionaryFilePath;

    /**
     * @var SplFixedArray
     */
    private $dictionary;

    /**
     * @param string $dictionaryFilePath
     *
     * @throws Exception
     */
    public function __construct($dictionaryFilePath)
    {
        $this->dictionaryFilePath = $dictionaryFilePath;
    }

    /**
     * Returns random word from dictionary.
     *
     * @return string
     */
    public function getRandWord()
    {
        if ($this->dictionary === null) {
            $this->readDictionary();
        }

        // mt_rand() here is not for cryptographic use.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $randIndex = mt_rand(0, count($this->dictionary) - 1);

        return trim($this->dictionary[$randIndex]);
    }

    /**
     * Read dictionary file.
     *
     * @throws Exception
     *
     * @return void
     */
    private function readDictionary()
    {
        if (! is_readable($this->dictionaryFilePath)) {
            throw new Exception(
                sprintf('Description file %s not found or is not readable', $this->dictionaryFilePath),
            );
        }

        $rows = file($this->dictionaryFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($rows === false) {
            throw new Exception(
                sprintf('Error occurred while reading dictionary file %s', $this->dictionaryFilePath),
            );
        }

        if (empty($rows)) {
            throw new Exception(
                sprintf('Dictionary file %s is empty', $this->dictionaryFilePath),
            );
        }

        $this->dictionary = SplFixedArray::fromArray($rows);
    }
}
