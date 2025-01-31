<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\FixtureGenerator;

use Magento\Framework\App\ResourceConnection;

/**
 * Class provides information about MySQL auto_increment configuration setting.
 */
class AutoIncrement
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var int
     */
    private $incrementValue;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get value of auto_increment_increment variable.
     *
     * @return int
     */
    public function getIncrement()
    {
        if ($this->incrementValue === null) {
            $increment = $this->resource->getConnection()->fetchRow('SHOW VARIABLES LIKE "auto_increment_increment"');
            $this->incrementValue = ! empty($increment['Value']) ? (int)$increment['Value'] : 1;
        }

        return $this->incrementValue;
    }
}
