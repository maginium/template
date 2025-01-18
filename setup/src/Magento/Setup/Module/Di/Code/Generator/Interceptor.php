<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Generator;

use Magento\Framework\Interception\Code\Generator\Interceptor as FrameworkInterceptor;
use ReflectionMethod;

class Interceptor extends FrameworkInterceptor
{
    /**
     * Intercepted methods list.
     *
     * @var array
     */
    private $interceptedMethods = [];

    /**
     * Sets list of intercepted methods.
     *
     * @param array $interceptedMethods
     *
     * @return void
     */
    public function setInterceptedMethods($interceptedMethods)
    {
        $this->interceptedMethods = $interceptedMethods;
    }

    /**
     * Whether method is intercepted.
     *
     * @param ReflectionMethod $method
     *
     * @return bool
     */
    protected function isInterceptedMethod(ReflectionMethod $method)
    {
        return parent::isInterceptedMethod($method) && in_array($method->getName(), $this->interceptedMethods);
    }
}
