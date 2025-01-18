<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Backend\Model\Url;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\Store;
use Magento\Ui\Model\Config as UiConfig;

/**
 * Model Class to Install User Configuration Data.
 */
class StoreConfigurationDataMapper
{
    /**#@+
     * Model data keys
     */
    public const KEY_USE_SEF_URL = 'use-rewrites';

    public const KEY_BASE_URL = 'base-url';

    public const KEY_BASE_URL_SECURE = 'base-url-secure';

    public const KEY_IS_SECURE = 'use-secure';

    public const KEY_IS_SECURE_ADMIN = 'use-secure-admin';

    public const KEY_LANGUAGE = 'language';

    public const KEY_TIMEZONE = 'timezone';

    public const KEY_CURRENCY = 'currency';

    public const KEY_ADMIN_USE_SECURITY_KEY = 'admin-use-security-key';

    public const KEY_JS_LOGGING = 'js-logging';
    // #@-

    // #@-
    private $pathDataMap = [
        Store::XML_PATH_USE_REWRITES => self::KEY_USE_SEF_URL,
        Store::XML_PATH_UNSECURE_BASE_URL => self::KEY_BASE_URL,
        Store::XML_PATH_SECURE_BASE_URL => self::KEY_BASE_URL_SECURE,
        Data::XML_PATH_DEFAULT_LOCALE => self::KEY_LANGUAGE,
        Store::XML_PATH_SECURE_IN_FRONTEND => self::KEY_IS_SECURE,
        Store::XML_PATH_SECURE_IN_ADMINHTML => self::KEY_IS_SECURE_ADMIN,
        Data::XML_PATH_DEFAULT_TIMEZONE => self::KEY_TIMEZONE,
        Currency::XML_PATH_CURRENCY_BASE => self::KEY_CURRENCY,
        Currency::XML_PATH_CURRENCY_DEFAULT => self::KEY_CURRENCY,
        Currency::XML_PATH_CURRENCY_ALLOW => self::KEY_CURRENCY,
        Url::XML_PATH_USE_SECURE_KEY => self::KEY_ADMIN_USE_SECURITY_KEY,
        UiConfig::XML_PATH_LOGGING => self::KEY_JS_LOGGING,
    ];

    /**
     * Gets All Configuration Data.
     *
     * @param array $installParamData
     *
     * @return array
     */
    public function getConfigData($installParamData)
    {
        $configData = [];

        foreach ($this->pathDataMap as $path => $key) {
            $configData = $this->addParamToConfigData($configData, $installParamData, $key, $path);
        }

        return $configData;
    }

    /**
     * Adds an install parameter value to the configData structure.
     *
     * @param array $configData
     * @param array $installParamData
     * @param string $key
     * @param string $path
     *
     * @return array
     */
    private function addParamToConfigData($configData, $installParamData, $key, $path)
    {
        if (isset($installParamData[$key])) {
            if (($key === self::KEY_BASE_URL) || ($key === self::KEY_BASE_URL_SECURE)) {
                $installParamData[$key] = rtrim($installParamData[$key], '/') . '/';
            }
            $configData[$path] = $installParamData[$key];
        }

        return $configData;
    }
}
