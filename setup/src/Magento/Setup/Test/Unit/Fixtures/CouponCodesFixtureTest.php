<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Fixtures;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\ResourceModel\Coupon\Collection as CouponCollection;
use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Setup\Fixtures\CartPriceRulesFixture;
use Magento\Setup\Fixtures\CouponCodesFixture;
use Magento\Setup\Fixtures\FixtureModel;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponCodesFixtureTest extends TestCase
{
    /**
     * @var CartPriceRulesFixture
     */
    private $model;

    /**
     * @var MockObject|FixtureModel
     */
    private $fixtureModelMock;

    /**
     * @var RuleFactory|MockObject
     */
    private $ruleFactoryMock;

    /**
     * @var CouponFactory|MockObject
     */
    private $couponCodeFactoryMock;

    /**
     * @var CouponCollectionFactory|MockObject
     */
    private $couponCollectionFactoryMock;

    /**
     * testExecute.
     *
     * @test
     */
    public function execute()
    {
        $couponCollectionMock = $this->createMock(CouponCollection::class);
        $this->couponCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponCollectionMock);
        $couponCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(0);

        $websiteMock = $this->createMock(Website::class);
        $websiteMock->expects($this->once())
            ->method('getId')
            ->willReturn('website_id');

        $storeManagerMock = $this->createMock(StoreManager::class);
        $storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn([$websiteMock]);

        $objectManagerMock = $this->createMock(ObjectManager::class);
        $objectManagerMock->expects($this->once())
            ->method('create')
            ->willReturn($storeManagerMock);

        $valueMap = [
            ['coupon_codes', 0, 1],
        ];

        $this->fixtureModelMock
            ->expects($this->exactly(1))
            ->method('getValue')
            ->willReturnMap($valueMap);
        $this->fixtureModelMock
            ->expects($this->exactly(1))
            ->method('getObjectManager')
            ->willReturn($objectManagerMock);

        $ruleMock = $this->createMock(Rule::class);
        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);

        $couponMock = $this->createMock(Coupon::class);
        $couponMock->expects($this->once())
            ->method('setRuleId')
            ->willReturnSelf();
        $couponMock->expects($this->once())
            ->method('setCode')
            ->willReturnSelf();
        $couponMock->expects($this->once())
            ->method('setIsPrimary')
            ->willReturnSelf();
        $couponMock->expects($this->once())
            ->method('setType')
            ->willReturnSelf();
        $couponMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->couponCodeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponMock);

        $this->model->execute();
    }

    /**
     * testNoFixtureConfigValue.
     *
     * @test
     */
    public function noFixtureConfigValue()
    {
        $couponCollectionMock = $this->createMock(CouponCollection::class);
        $this->couponCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponCollectionMock);
        $couponCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(0);

        $ruleMock = $this->createMock(Rule::class);
        $ruleMock->expects($this->never())->method('save');

        $objectManagerMock = $this->createMock(ObjectManager::class);
        $objectManagerMock->expects($this->never())
            ->method('get')
            ->with(Rule::class)
            ->willReturn($ruleMock);

        $this->fixtureModelMock
            ->expects($this->never())
            ->method('getObjectManager')
            ->willReturn($objectManagerMock);
        $this->fixtureModelMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(false);

        $this->model->execute();
    }

    /**
     * @test
     */
    public function fixtureAlreadyCreated()
    {
        $couponCollectionMock = $this->createMock(CouponCollection::class);
        $this->couponCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponCollectionMock);
        $couponCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(1);

        $this->fixtureModelMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(1);

        $this->fixtureModelMock->expects($this->never())->method('getObjectManager');
        $this->ruleFactoryMock->expects($this->never())->method('create');
        $this->couponCodeFactoryMock->expects($this->never())->method('create');

        $this->model->execute();
    }

    /**
     * testGetActionTitle.
     *
     * @test
     */
    public function getActionTitle()
    {
        $this->assertSame('Generating coupon codes', $this->model->getActionTitle());
    }

    /**
     * testIntroduceParamLabels.
     *
     * @test
     */
    public function introduceParamLabels()
    {
        $this->assertSame(['coupon_codes' => 'Coupon Codes'], $this->model->introduceParamLabels());
    }

    /**
     * setUp.
     */
    protected function setUp(): void
    {
        $this->fixtureModelMock = $this->createMock(FixtureModel::class);
        $this->ruleFactoryMock = $this->createPartialMock(RuleFactory::class, ['create']);
        $this->couponCodeFactoryMock = $this->createPartialMock(
            CouponFactory::class,
            ['create'],
        );
        $this->couponCollectionFactoryMock = $this->createMock(CouponCollectionFactory::class);
        $this->model = new CouponCodesFixture(
            $this->fixtureModelMock,
            $this->ruleFactoryMock,
            $this->couponCodeFactoryMock,
            $this->couponCollectionFactoryMock,
        );
    }
}
