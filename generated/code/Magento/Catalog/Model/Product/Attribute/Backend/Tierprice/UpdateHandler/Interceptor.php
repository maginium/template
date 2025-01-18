<?php
namespace Magento\Catalog\Model\Product\Attribute\Backend\TierPrice\UpdateHandler;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Product\Attribute\Backend\TierPrice\UpdateHandler
 */
class Interceptor extends \Magento\Catalog\Model\Product\Attribute\Backend\TierPrice\UpdateHandler implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository, \Magento\Customer\Api\GroupManagementInterface $groupManagement, \Magento\Framework\EntityManager\MetadataPool $metadataPool, \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice $tierPriceResource, ?\Magento\Framework\Locale\FormatInterface $localeFormat = null)
    {
        $this->___init();
        parent::__construct($storeManager, $attributeRepository, $groupManagement, $metadataPool, $tierPriceResource, $localeFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute($entity, $arguments);
    }
}
