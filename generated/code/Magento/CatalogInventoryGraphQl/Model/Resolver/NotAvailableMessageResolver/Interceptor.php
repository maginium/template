<?php
namespace Magento\CatalogInventoryGraphQl\Model\Resolver\NotAvailableMessageResolver;

/**
 * Interceptor class for @see \Magento\CatalogInventoryGraphQl\Model\Resolver\NotAvailableMessageResolver
 */
class Interceptor extends \Magento\CatalogInventoryGraphQl\Model\Resolver\NotAvailableMessageResolver implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\QuoteGraphQl\Model\CartItem\ProductStock $productStock)
    {
        $this->___init();
        parent::__construct($scopeConfig, $productStock);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(\Magento\Framework\GraphQl\Config\Element\Field $field, $context, \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        return $pluginInfo ? $this->___callPlugins('resolve', func_get_args(), $pluginInfo) : parent::resolve($field, $context, $info, $value, $args);
    }
}
