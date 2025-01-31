<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

/**
 * Generates a crypt.
 */
class CryptKeyGenerator implements CryptKeyGeneratorInterface
{
    /**
     * @var Random
     */
    private $random;

    /**
     * CryptKeyGenerator constructor.
     *
     * @param Random $random
     */
    public function __construct(Random $random)
    {
        $this->random = $random;
    }

    /**
     * Generates & returns a string to be used as crypt key.
     *
     * @throws LocalizedException
     *
     * @return string
     */
    public function generate()
    {
        return $this->getRandomString();
    }

    /**
     * Returns a random string.
     *
     * @throws LocalizedException
     *
     * @return string
     */
    private function getRandomString()
    {
        return ConfigOptionsListConstants::STORE_KEY_ENCODED_RANDOM_STRING_PREFIX .
            $this->random->getRandomBytes(ConfigOptionsListConstants::STORE_KEY_RANDOM_STRING_SIZE);
    }
}
