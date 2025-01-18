<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * License file reader.
 */
class License
{
    /**
     * Default License File location.
     *
     * @var string
     */
    public const DEFAULT_LICENSE_FILENAME = 'LICENSE.txt';

    /**
     * License File location.
     *
     * @var string
     */
    public const LICENSE_FILENAME = 'LICENSE_EE.txt';

    /**
     * Directory that contains license file.
     *
     * @var ReadInterface
     */
    private $dir;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->dir = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Returns contents of License file.
     *
     * @return string|bool
     */
    public function getContents()
    {
        if ($this->dir->isFile(self::LICENSE_FILENAME)) {
            return $this->dir->readFile(self::LICENSE_FILENAME);
        }

        if ($this->dir->isFile(self::DEFAULT_LICENSE_FILENAME)) {
            return $this->dir->readFile(self::DEFAULT_LICENSE_FILENAME);
        }

        return false;
    }
}
