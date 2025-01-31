<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model\FixtureGenerator;

use DateTime;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Product template generator.
 */
class CustomerTemplateGenerator implements TemplateEntityGeneratorInterface
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RegionCollectionFactory
     */
    private $regionsCollectionFactory;

    /**
     * @param CustomerFactory $customerFactory
     * @param AddressFactory $addressFactory
     * @param StoreManagerInterface $storeManager
     * @param RegionCollectionFactory|null $regionsCollectionFactory
     */
    public function __construct(
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory,
        StoreManagerInterface $storeManager,
        ?RegionCollectionFactory $regionsCollectionFactory = null,
    ) {
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->storeManager = $storeManager;
        $this->regionsCollectionFactory = $regionsCollectionFactory ?: ObjectManager::getInstance()->get(
            RegionCollectionFactory::class,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateEntity()
    {
        $customer = $this->getCustomerTemplate();
        $customer->save();
        $address = $this->getAddressTemplate($customer->getId());
        $address->save();

        return $customer;
    }

    /**
     * Get customer template.
     *
     * @return Customer
     */
    private function getCustomerTemplate()
    {
        $customerRandomizerNumber = crc32(random_int(1, PHP_INT_MAX));

        $now = new DateTime;

        return $this->customerFactory->create([
            'data' => [
                'email' => sprintf('user_%s@example.com', $customerRandomizerNumber),
                'confirmation' => null,
                'created_at' => $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                'created_in' => 'Default',
                'default_billing' => '1',
                'default_shipping' => '1',
                'disable_auto_group_change' => '0',
                'dob' => '12-10-1991',
                'firstname' => 'Firstname',
                'group_id' => '1',
                'lastname' => 'Lastname',
                'middlename' => '',
                'password_hash' => '',
                'prefix' => null,
                'rp_token' => null,
                'rp_token_created_at' => null,
                'store_id' => $this->storeManager->getDefaultStoreView()->getId(),
                'suffix' => null,
                'taxvat' => null,
                'website_id' => $this->storeManager->getDefaultStoreView()->getWebsiteId(),
                'password' => '123123q',
            ],
        ]);
    }

    /**
     * Get address template.
     *
     * @param int $customerId
     *
     * @return Address
     */
    private function getAddressTemplate($customerId)
    {
        return $this->addressFactory->create([
            'data' => [
                'parent_id' => $customerId,
                'attribute_set_id' => 2,
                'telephone' => 3468676,
                'postcode' => 75477,
                'country_id' => 'US',
                'city' => 'CityM',
                'company' => 'CompanyName',
                'street' => 'Green str, 67',
                'lastname' => 'Smith',
                'firstname' => 'John',
                'region_id' => $this->getFirstRegionId(),
                'fax' => '04040404',
                'middlename' => '',
                'prefix' => '',
                'region' => 'Arkansas',
                'suffix' => '',
                'vat_id' => '',
                'default_billing_' => '1',
                'default_shipping_' => '1',
            ],
        ]);
    }

    /**
     * Get first region id.
     *
     * @return mixed
     */
    private function getFirstRegionId()
    {
        $regionsCollection = $this->regionsCollectionFactory->create();
        $regionsCollection->unshiftOrder('region_id', 'ASC');
        $region = $regionsCollection->getFirstItem();

        return $region->getRegionId();
    }
}
