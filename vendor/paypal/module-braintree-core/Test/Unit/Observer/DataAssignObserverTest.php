<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PayPal\Braintree\Test\Unit\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use PayPal\Braintree\Observer\DataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class DataAssignObserverTest extends TestCase
{
    private const string PAYMENT_METHOD_NONCE = 'nonce';
    private const string DEVICE_DATA = '{"test": "test"}';

    /**
     * @throws Exception
     */
    public function testExecute()
    {
        // Mock the Observer and Event classes
        $observerContainer = $this->createMock(Event\Observer::class);
        $event = $this->createMock(Event::class);

        // Mock the Payment Info model
        $paymentInfoModel = $this->createMock(InfoInterface::class);

        // Create the DataObject with additional payment information
        $dataObject = new DataObject([
            PaymentInterface::KEY_ADDITIONAL_DATA => [
                'payment_method_nonce' => self::PAYMENT_METHOD_NONCE,
                'device_data' => self::DEVICE_DATA
            ]
        ]);

        // Define the expected method calls and return values
        $observerContainer->method('getEvent')->willReturn($event);
        $event->method('getDataByKey')->willReturnMap([
            [AbstractDataAssignObserver::MODEL_CODE, $paymentInfoModel],
            [AbstractDataAssignObserver::DATA_CODE, $dataObject]
        ]);

        // Set up a callback to manually verify the arguments for setAdditionalInformation
        $paymentInfoModel->expects($this->exactly(2))
            ->method('setAdditionalInformation')
            ->willReturnCallback(function ($key, $value) {
                if ($key === 'payment_method_nonce') {
                    $this->assertEquals(self::PAYMENT_METHOD_NONCE, $value);
                } elseif ($key === 'device_data') {
                    $this->assertEquals(self::DEVICE_DATA, $value);
                } else {
                    $this->fail('Unexpected key: ' . $key);
                }
            });

        // Create and execute the observer
        $observer = new DataAssignObserver();
        $observer->execute($observerContainer);
    }
}
