<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Fixtures;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Website and category provider.
 */
class WebsiteCategoryProvider
{
    /**
     * @var array
     */
    private $categoriesPerWebsite;

    /**
     * @var FixtureConfig
     */
    private $fixtureConfig;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var array
     */
    private $websites;

    /**
     * @var array
     */
    private $categories;

    /**
     * @param FixtureConfig $fixtureConfig
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        FixtureConfig $fixtureConfig,
        ResourceConnection $resourceConnection,
    ) {
        $this->fixtureConfig = $fixtureConfig;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get websites for $productIndex product.
     *
     * @param int $productIndex Index of generated product
     *
     * @throws Exception
     *
     * @return array
     */
    public function getWebsiteIds($productIndex)
    {
        if ($this->isAssignToAllWebsites()) {
            return $this->getAllWebsites();
        }
        $categoriesPerWebsite = $this->getCategoriesAndWebsites();

        if (! count($categoriesPerWebsite)) {
            throw new Exception('Cannot find categories. Please, be sure that you have generated categories');
        }

        return [$categoriesPerWebsite[$productIndex % count($categoriesPerWebsite)]['website']];
    }

    /**
     * Get product if for $productIndex product.
     *
     * @param int $productIndex
     *
     * @return int
     */
    public function getCategoryId($productIndex)
    {
        if ($this->isAssignToAllWebsites()) {
            $categories = $this->getAllCategories();

            return $categories[$productIndex % count($categories)];
        }
        $categoriesPerWebsite = $this->getCategoriesAndWebsites();

        return $categoriesPerWebsite[$productIndex % count($categoriesPerWebsite)]['category'];
    }

    /**
     * Get categories and websites.
     *
     * @return array
     */
    private function getCategoriesAndWebsites()
    {
        if ($this->categoriesPerWebsite === null) {
            $select = $this->getConnection()->select()
                ->from(
                    ['c' => $this->resourceConnection->getTableName('catalog_category_entity')],
                    ['category' => 'entity_id'],
                )->join(
                    ['sg' => $this->resourceConnection->getTableName('store_group')],
                    "c.path like concat('1/', sg.root_category_id, '/%')",
                    ['website' => 'website_id'],
                )->order('category ASC');
            $this->categoriesPerWebsite = $this->getConnection()->fetchAll($select);
        }

        return $this->categoriesPerWebsite;
    }

    /**
     * Checks is assign_entities_to_all_websites flag set.
     *
     * @return bool
     */
    private function isAssignToAllWebsites()
    {
        return (bool)$this->fixtureConfig->getValue('assign_entities_to_all_websites', false);
    }

    /**
     * Provides all websites.
     *
     * @return array
     */
    private function getAllWebsites()
    {
        if ($this->websites === null) {
            $this->websites = array_unique(array_column($this->getCategoriesAndWebsites(), 'website'));
        }

        return $this->websites;
    }

    /**
     * Provides all categories.
     *
     * @return array
     */
    private function getAllCategories()
    {
        if ($this->categories === null) {
            $this->categories = array_values(array_unique(array_column($this->getCategoriesAndWebsites(), 'category')));
        }

        return $this->categories;
    }

    /**
     * Provides connection.
     *
     * @return AdapterInterface
     */
    private function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }
}
