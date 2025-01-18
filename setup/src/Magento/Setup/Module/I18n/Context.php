<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n;

use InvalidArgumentException;
use Magento\Framework\Component\ComponentRegistrar;

/**
 *  Context.
 */
class Context
{
    /**
     * Locale directory.
     */
    public const LOCALE_DIRECTORY = 'i18n';

    /**#@+
     * Context info
     */
    public const CONTEXT_TYPE_MODULE = 'module';

    public const CONTEXT_TYPE_THEME = 'theme';

    public const CONTEXT_TYPE_LIB = 'lib';

    // #@-

    // #@-
    private $componentRegistrar;

    /**
     * Constructor.
     *
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(ComponentRegistrar $componentRegistrar)
    {
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * Get context from file path in array(<context type>, <context value>) format
     * - for module: <Namespace>_<module name>
     * - for theme: <area>/<theme name>
     * - for pub: relative path to file.
     *
     * @param string $path
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getContextByPath($path)
    {
        if ($value = $this->getComponentName(ComponentRegistrar::MODULE, $path)) {
            $type = self::CONTEXT_TYPE_MODULE;
        } elseif ($value = $this->getComponentName(ComponentRegistrar::THEME, $path)) {
            $type = self::CONTEXT_TYPE_THEME;
        } elseif ($value = mb_strstr($path, '/lib/web/')) {
            $type = self::CONTEXT_TYPE_LIB;
            $value = ltrim($value, '/');
        } else {
            throw new InvalidArgumentException(sprintf('Invalid path given: "%s".', $path));
        }

        return [$type, $value];
    }

    /**
     * Get paths by context.
     *
     * @param string $type
     * @param array $value
     *
     * @throws InvalidArgumentException
     *
     * @return string|null
     */
    public function buildPathToLocaleDirectoryByContext($type, $value)
    {
        switch ($type) {
            case self::CONTEXT_TYPE_MODULE:
                $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $value);

                break;

            case self::CONTEXT_TYPE_THEME:
                $path = $this->componentRegistrar->getPath(ComponentRegistrar::THEME, $value);

                break;

            case self::CONTEXT_TYPE_LIB:
                $path = BP . '/lib/web';

                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid context given: "%s".', $type));
        }

        return ($path === null) ? null : $path . '/' . self::LOCALE_DIRECTORY . '/';
    }

    /**
     * Try to get component name by path, return false if not found.
     *
     * @param string $componentType
     * @param string $path
     *
     * @return bool|string
     */
    private function getComponentName($componentType, $path)
    {
        foreach ($this->componentRegistrar->getPaths($componentType) as $componentName => $componentDir) {
            $componentDir .= '/';

            if (str_contains($path, $componentDir)) {
                return $componentName;
            }
        }

        return false;
    }
}
