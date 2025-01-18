<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\Description;

use Magento\Setup\Model\Description\Mixin\MixinFactory;

/**
 * Apply mixin to description.
 */
class MixinManager
{
    /**
     * @var MixinFactory
     */
    private $mixinFactory;

    /**
     * @param MixinFactory $mixinFactory
     */
    public function __construct(MixinFactory $mixinFactory)
    {
        $this->mixinFactory = $mixinFactory;
    }

    /**
     * Apply list of mixin to description.
     *
     * @param string $description
     * @param array $mixinList
     *
     * @return mixed
     */
    public function apply($description, array $mixinList)
    {
        foreach ($mixinList as $mixinType) {
            $description = $this->mixinFactory->create($mixinType)->apply($description);
        }

        return $description;
    }
}
