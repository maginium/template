<?php
namespace Magento\ReCaptchaWebapiGraphQl\Model\Resolver\ReCaptchaFormConfig;

/**
 * Interceptor class for @see \Magento\ReCaptchaWebapiGraphQl\Model\Resolver\ReCaptchaFormConfig
 */
class Interceptor extends \Magento\ReCaptchaWebapiGraphQl\Model\Resolver\ReCaptchaFormConfig implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\ReCaptchaFrontendUi\Model\CaptchaTypeResolver $captchaTypeResolver, \Magento\ReCaptchaFrontendUi\Model\ErrorMessageConfig $errorMessageConfig, array $providers = [], array $formTypes = [])
    {
        $this->___init();
        parent::__construct($captchaTypeResolver, $errorMessageConfig, $providers, $formTypes);
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
