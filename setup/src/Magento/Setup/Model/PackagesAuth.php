<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

/**
 * Class PackagesAuth contains auth details.
 */
class PackagesAuth
{
    /**#@+
     * Composer auth.json keys
     */
    public const KEY_HTTPBASIC = 'http-basic';

    public const KEY_USERNAME = 'username';

    public const KEY_PASSWORD = 'password';
    // #@-

    /**#@+
     * Filenames for auth and package info
     */
    public const PATH_TO_AUTH_FILE = 'auth.json';

    public const PATH_TO_PACKAGES_FILE = 'packages.json';
    // #@-
}
