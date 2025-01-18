<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model\Description\Mixin;

use Magento\Setup\Model\Description\Mixin\BoldMixin;
use Magento\Setup\Model\Description\Mixin\Helper\RandomWordSelector;
use Magento\Setup\Model\Description\Mixin\Helper\WordWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BoldMixinTest extends TestCase
{
    /**
     * @var BoldMixin
     */
    private $mixin;

    /**
     * @var MockObject|RandomWordSelector
     */
    private $randomWordSelectorMock;

    /**
     * @var MockObject|WordWrapper
     */
    private $wordWrapperMock;

    /**
     * @test
     */
    public function emptyApply()
    {
        $this->assertEquals('', $this->mixin->apply(''));
    }

    /**
     * @test
     */
    public function apply()
    {
        $fixtureString = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
        $fixtureStringResult = '<b>Lorem</b> ipsum <b>dolor</b> sit amet, consectetur adipiscing elit.';
        $randWordsFixture = ['Lorem', 'dolor'];

        $this->randomWordSelectorMock
            ->expects($this->once())
            ->method('getRandomWords')
            ->with($fixtureString, $this->greaterThan(0))
            ->willReturn($randWordsFixture);

        $this->wordWrapperMock
            ->expects($this->once())
            ->method('wrapWords')
            ->with($fixtureString, $randWordsFixture, '<b>%s</b>')
            ->willReturn($fixtureStringResult);

        $this->assertEquals($fixtureStringResult, $this->mixin->apply($fixtureString));
    }

    protected function setUp(): void
    {
        $this->randomWordSelectorMock =
            $this->createMock(RandomWordSelector::class);
        $this->wordWrapperMock = $this->createMock(WordWrapper::class);

        $this->mixin = new BoldMixin(
            $this->randomWordSelectorMock,
            $this->wordWrapperMock,
        );
    }
}
