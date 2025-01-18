<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Model;

use Magento\Setup\Exception;

/**
 * Creates instance of Magento\Setup\Model\SearchConfig class.
 */
class SearchConfigFactory
{
    /**
     * @var ObjectManagerProvider
     */
    private $objectManagerProvider;

    /**
     * @param ObjectManagerProvider $objectManagerProvider
     */
    public function __construct(ObjectManagerProvider $objectManagerProvider)
    {
        $this->objectManagerProvider = $objectManagerProvider;
    }

    /**
     * Create SearchConfig instance.
     *
     * @throws Exception
     *
     * @return SearchConfig
     */
    public function create(): SearchConfig
    {
        return $this->objectManagerProvider->get()->create(SearchConfig::class);
    }
}
