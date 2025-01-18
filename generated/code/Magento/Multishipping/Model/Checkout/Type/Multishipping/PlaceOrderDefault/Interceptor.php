<?php
namespace Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderDefault;

/**
 * Interceptor class for @see \Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderDefault
 */
class Interceptor extends \Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderDefault implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Sales\Api\OrderManagementInterface $orderManagement)
    {
        $this->___init();
        parent::__construct($orderManagement);
    }

    /**
     * {@inheritdoc}
     */
    public function place(array $orderList) : array
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'place');
        return $pluginInfo ? $this->___callPlugins('place', func_get_args(), $pluginInfo) : parent::place($orderList);
    }
}
