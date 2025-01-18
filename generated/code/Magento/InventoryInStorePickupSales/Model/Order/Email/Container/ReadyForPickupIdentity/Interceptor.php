<?php
namespace Magento\InventoryInStorePickupSales\Model\Order\Email\Container\ReadyForPickupIdentity;

/**
 * Interceptor class for @see \Magento\InventoryInStorePickupSales\Model\Order\Email\Container\ReadyForPickupIdentity
 */
class Interceptor extends \Magento\InventoryInStorePickupSales\Model\Order\Email\Container\ReadyForPickupIdentity implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($scopeConfig, $storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEnabled');
        return $pluginInfo ? $this->___callPlugins('isEnabled', func_get_args(), $pluginInfo) : parent::isEnabled();
    }
}
