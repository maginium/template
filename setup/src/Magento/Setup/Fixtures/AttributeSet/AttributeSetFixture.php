<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\AttributeSet;

use Exception;
use Magento\Catalog\Api\AttributeSetManagementInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeGroupRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterface;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Eav\Api\Data\AttributeSetInterfaceFactory;

/**
 * Persitor for Attribute Sets and Attributes based on the configuration.
 */
class AttributeSetFixture
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var ProductAttributeManagementInterface
     */
    private $attributeManagement;

    /**
     * @var ProductAttributeInterfaceFactory
     */
    private $attributeFactory;

    /**
     * @var AttributeOptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var AttributeSetInterfaceFactory
     */
    private $attributeSetFactory;

    /**
     * @var AttributeGroupInterfaceFactory
     */
    private $attributeGroupFactory;

    /**
     * @var AttributeSetManagementInterface
     */
    private $attributeSetManagement;

    /**
     * @var ProductAttributeGroupRepositoryInterface
     */
    private $attributeGroupRepository;

    /**
     * AttributeSetsFixture constructor.
     *
     * @param AttributeSetManagementInterface $attributeSetManagement
     * @param ProductAttributeGroupRepositoryInterface $attributeGroupRepository
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param ProductAttributeManagementInterface $attributeManagement
     * @param ProductAttributeInterfaceFactory $attributeFactory
     * @param AttributeOptionInterfaceFactory $optionFactory
     * @param AttributeSetInterfaceFactory $attributeSetFactory
     * @param AttributeGroupInterfaceFactory $attributeGroupFactory
     */
    public function __construct(
        AttributeSetManagementInterface $attributeSetManagement,
        ProductAttributeGroupRepositoryInterface $attributeGroupRepository,
        ProductAttributeRepositoryInterface $attributeRepository,
        ProductAttributeManagementInterface $attributeManagement,
        ProductAttributeInterfaceFactory $attributeFactory,
        AttributeOptionInterfaceFactory $optionFactory,
        AttributeSetInterfaceFactory $attributeSetFactory,
        AttributeGroupInterfaceFactory $attributeGroupFactory,
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->attributeManagement = $attributeManagement;
        $this->attributeFactory = $attributeFactory;
        $this->optionFactory = $optionFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * Create Attribute Set based on raw data.
     *
     * @param array $attributeSetData
     * @param int $sortOrder
     *
     * @return array
     */
    public function createAttributeSet(array $attributeSetData, $sortOrder = 3)
    {
        /** @var AttributeSetInterface $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setAttributeSetName($attributeSetData['name']);
        $attributeSet->setEntityTypeId(ProductAttributeInterface::ENTITY_TYPE_CODE);

        try {
            $attributeSet = $this->attributeSetManagement->create($attributeSet, 4);
        } catch (Exception $e) {
            return $this->getFormattedAttributeSetData($attributeSetData);
        }
        $attributeSetId = $attributeSet->getAttributeSetId();

        /** @var AttributeGroupInterface $attributeGroup */
        $attributeGroup = $this->attributeGroupFactory->create();
        $attributeGroup->setAttributeGroupName($attributeSet->getAttributeSetName() . ' - Group');
        $attributeGroup->setAttributeSetId($attributeSetId);
        $this->attributeGroupRepository->save($attributeGroup);
        $attributeGroupId = $attributeGroup->getAttributeGroupId();

        $attributesData = array_key_exists(0, $attributeSetData['attributes']['attribute'])
            ? $attributeSetData['attributes']['attribute'] : [$attributeSetData['attributes']['attribute']];

        foreach ($attributesData as $attributeData) {
            //Create Attribute
            $optionsData = array_key_exists(0, $attributeData['options']['option'])
                ? $attributeData['options']['option'] : [$attributeData['options']['option']];
            $options = [];

            foreach ($optionsData as $optionData) {
                $option = $this->optionFactory->create(['data' => $optionData]);
                $options[] = $option;
            }

            /** @var ProductAttributeInterface $attribute */
            $attribute = $this->attributeFactory->create(['data' => $attributeData]);
            $attribute->setOptions($options);
            $attribute->setNote('auto');

            $productAttribute = $this->attributeRepository->save($attribute);
            $attributeId = $productAttribute->getAttributeId();

            //Associate Attribute to Attribute Set
            $this->attributeManagement->assign($attributeSetId, $attributeGroupId, $attributeId, $sortOrder);
        }

        return $this->getFormattedAttributeSetData($attributeSetData);
    }

    /**
     * Return formatted attribute set data.
     *
     * @param array $attributeSetData
     *
     * @return array
     */
    private function getFormattedAttributeSetData($attributeSetData)
    {
        $attributesData = array_key_exists(0, $attributeSetData['attributes']['attribute'])
            ? $attributeSetData['attributes']['attribute'] : [$attributeSetData['attributes']['attribute']];
        $attributes = [];

        foreach ($attributesData as $attributeData) {
            $optionsData = array_key_exists(0, $attributeData['options']['option'])
                ? $attributeData['options']['option'] : [$attributeData['options']['option']];
            $optionsData = array_map(fn($option) => $option['label'], $optionsData);
            $attributes[] = [
                'name' => $attributeData['attribute_code'],
                'values' => $optionsData,
            ];
        }

        return [
            'name' => $attributeSetData['name'],
            'attributes' => $attributes,
        ];
    }
}
