<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Scanner;

use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\FileResolver;

class ConfigurationScanner
{
    /**
     * @var FileResolver
     */
    private $fileResolver;

    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * ConfigurationScanner constructor.
     *
     * @param FileResolver $fileResolver
     * @param AreaList $areaList
     */
    public function __construct(
        FileResolver $fileResolver,
        AreaList $areaList,
    ) {
        $this->fileResolver = $fileResolver;
        $this->areaList = $areaList;
    }

    /**
     * Scan configuration files.
     *
     * @param string $fileName
     *
     * @return array of paths to the configuration files
     */
    public function scan($fileName)
    {
        $files = [];
        $areaCodes = array_merge(
            ['primary', Area::AREA_GLOBAL],
            $this->areaList->getCodes(),
        );

        foreach ($areaCodes as $area) {
            $files = array_merge_recursive(
                $files,
                $this->fileResolver->get($fileName, $area)->toArray(),
            );
        }

        return array_keys($files);
    }
}
