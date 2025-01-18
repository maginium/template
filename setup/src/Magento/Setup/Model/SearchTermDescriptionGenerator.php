<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Magento\Setup\Model\Description\DescriptionGenerator;

/**
 * Class SearchTermDescriptionGenerator.
 *
 * Class responsible for generation description
 * and applying search terms to it
 */
class SearchTermDescriptionGenerator implements DescriptionGeneratorInterface
{
    /**
     * @var DescriptionGenerator
     */
    private $descriptionGenerator;

    /**
     * @var SearchTermManager
     */
    private $searchTermManager;

    /**
     * @var string
     */
    private $cachedDescription;

    /**
     * @param DescriptionGenerator $descriptionGenerator
     * @param SearchTermManager $searchTermManager
     */
    public function __construct(
        DescriptionGenerator $descriptionGenerator,
        SearchTermManager $searchTermManager,
    ) {
        $this->descriptionGenerator = $descriptionGenerator;
        $this->searchTermManager = $searchTermManager;
    }

    /**
     * Generate description with search terms.
     *
     * @param int $currentProductIndex
     *
     * @return string
     */
    public function generate($currentProductIndex)
    {
        $description = $this->getDescription();
        $this->searchTermManager->applySearchTermsToDescription($description, (int)$currentProductIndex);

        return $description;
    }

    /**
     * Generate new description or use cached one.
     *
     * @param bool $useCachedDescription
     *
     * @return string
     */
    private function getDescription($useCachedDescription = true)
    {
        if ($useCachedDescription !== true || $this->cachedDescription === null) {
            $this->cachedDescription = $this->descriptionGenerator->generate();
        }

        return $this->cachedDescription;
    }
}
