<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures;

use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\StoreManager;

/**
 * Fixture for generating coupon codes.
 *
 * Support the following format:
 * <!-- Number of coupon codes -->
 * <coupon_codes>{int}</coupon_codes>
 *
 * @see setup/performance-toolkit/profiles/ce/small.xml
 */
class CouponCodesFixture extends Fixture
{
    /**
     * @var int
     */
    protected $priority = 129;

    /**
     * @var int
     */
    protected $couponCodesCount = 0;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var CouponFactory
     */
    private $couponCodeFactory;

    /**
     * @var CouponCollectionFactory
     */
    private $couponCollectionFactory;

    /**
     * Constructor.
     *
     * @param FixtureModel $fixtureModel
     * @param RuleFactory|null $ruleFactory
     * @param CouponFactory|null $couponCodeFactory
     * @param CouponCollectionFactory|null $couponCollectionFactory
     */
    public function __construct(
        FixtureModel $fixtureModel,
        ?RuleFactory $ruleFactory = null,
        ?CouponFactory $couponCodeFactory = null,
        ?CouponCollectionFactory $couponCollectionFactory = null,
    ) {
        parent::__construct($fixtureModel);
        $this->ruleFactory = $ruleFactory ?: $this->fixtureModel->getObjectManager()
            ->get(RuleFactory::class);
        $this->couponCodeFactory = $couponCodeFactory ?: $this->fixtureModel->getObjectManager()
            ->get(CouponFactory::class);
        $this->couponCollectionFactory = $couponCollectionFactory ?: $this->fixtureModel->getObjectManager()
            ->get(CouponCollectionFactory::class);
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $requestedCouponsCount = (int)$this->fixtureModel->getValue('coupon_codes', 0);
        $existedCouponsCount = $this->couponCollectionFactory->create()->getSize();
        $this->couponCodesCount = max(0, $requestedCouponsCount - $existedCouponsCount);

        if (! $this->couponCodesCount) {
            return;
        }

        /** @var StoreManager $storeManager */
        $storeManager = $this->fixtureModel->getObjectManager()->create(StoreManager::class);

        //Get all websites
        $websitesArray = [];
        $websites = $storeManager->getWebsites();

        foreach ($websites as $website) {
            $websitesArray[] = $website->getId();
        }

        $this->generateCouponCodes($this->ruleFactory, $this->couponCodeFactory, $websitesArray);
    }

    /**
     * Generate Coupon Codes.
     *
     * @param RuleFactory $ruleFactory
     * @param CouponFactory $couponCodeFactory
     * @param array $websitesArray
     *
     * @return void
     */
    public function generateCouponCodes($ruleFactory, $couponCodeFactory, $websitesArray)
    {
        for ($i = 0; $i < $this->couponCodesCount; $i++) {
            $ruleName = sprintf('Coupon Code %1$d', $i);
            $data = [
                'rule_id' => null,
                'name' => $ruleName,
                'is_active' => '1',
                'website_ids' => $websitesArray,
                'customer_group_ids' => [
                    0 => '0',
                    1 => '1',
                    2 => '2',
                    3 => '3',
                ],
                'coupon_type' => Rule::COUPON_TYPE_SPECIFIC,
                'conditions' => [],
                'simple_action' => Rule::BY_PERCENT_ACTION,
                'discount_amount' => 5,
                'discount_step' => 0,
                'stop_rules_processing' => 1,
                'sort_order' => '5',
            ];

            $model = $ruleFactory->create();
            $model->loadPost($data);
            $useAutoGeneration = (int)! empty($data['use_auto_generation']);
            $model->setUseAutoGeneration($useAutoGeneration);
            $model->save();

            $coupon = $couponCodeFactory->create();
            $coupon->setRuleId($model->getId())
                ->setCode('CouponCode' . $i)
                ->setIsPrimary(true)
                ->setType(0);
            $coupon->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle()
    {
        return 'Generating coupon codes';
    }

    /**
     * {@inheritdoc}
     */
    public function introduceParamLabels()
    {
        return [
            'coupon_codes' => 'Coupon Codes',
        ];
    }
}
