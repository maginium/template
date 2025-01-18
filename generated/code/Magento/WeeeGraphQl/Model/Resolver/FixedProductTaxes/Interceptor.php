<?php
namespace Magento\WeeeGraphQl\Model\Resolver\FixedProductTaxes;

/**
 * Interceptor class for @see \Magento\WeeeGraphQl\Model\Resolver\FixedProductTaxes
 */
class Interceptor extends \Magento\WeeeGraphQl\Model\Resolver\FixedProductTaxes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\WeeeGraphQl\Model\FixedProductTaxes\PricesProvider $pricesProvider)
    {
        $this->___init();
        parent::__construct($pricesProvider);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(\Magento\Framework\GraphQl\Config\Element\Field $field, $context, \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info, ?array $value = null, ?array $args = null) : array
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        return $pluginInfo ? $this->___callPlugins('resolve', func_get_args(), $pluginInfo) : parent::resolve($field, $context, $info, $value, $args);
    }
}
