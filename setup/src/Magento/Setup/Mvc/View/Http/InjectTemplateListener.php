<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Mvc\View\Http;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\View\Http\InjectTemplateListener as LaminasInjectTemplateListener;

/**
 * InjectTemplateListener for HTTP request.
 */
class InjectTemplateListener extends LaminasInjectTemplateListener
{
    /**
     * Inject a template into the view model, if none present.
     *
     * Template is derived from the controller found in the route match, and,
     * optionally, the action, if present.
     *
     * @param  MvcEvent $e
     *
     * @return void
     */
    public function injectTemplate(MvcEvent $e)
    {
        $e->getRouteMatch()->setParam('action', null);
        parent::injectTemplate($e);
    }

    /**
     * Determine the top-level namespace of the controller.
     *
     * @param  string $controller
     *
     * @return string
     */
    protected function deriveModuleNamespace($controller)
    {
        if (! mb_strstr($controller, '\\')) {
            return '';
        }

        // Retrieve the first two elemenents representing the vendor and module name.
        $nsArray = explode('\\', $controller);
        $subNsArray = array_slice($nsArray, 0, 2);

        return implode('/', $subNsArray);
    }

    /**
     * Get controller sub-namespace.
     *
     * @param string $namespace
     *
     * @return string
     */
    protected function deriveControllerSubNamespace($namespace)
    {
        if (! mb_strstr($namespace, '\\')) {
            return '';
        }
        $nsArray = explode('\\', $namespace);

        // Remove the first three elements representing the vendor, module name and controller directory.
        $subNsArray = array_slice($nsArray, 3);

        if (empty($subNsArray)) {
            return '';
        }

        return implode('/', $subNsArray);
    }
}
