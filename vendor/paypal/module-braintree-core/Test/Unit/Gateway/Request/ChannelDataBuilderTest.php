<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PayPal\Braintree\Test\Unit\Gateway\Request;

use PayPal\Braintree\Gateway\Request\ChannelDataBuilder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ChannelDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ChannelDataBuilder
     */
    private ChannelDataBuilder $builder;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->builder = new ChannelDataBuilder();
    }

    public function testBuild()
    {
        $expected = [
            'channel' => 'Magento2GeneBT'
        ];
        self::assertEquals($expected, $this->builder->build([]));
    }
}
