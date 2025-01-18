<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\I18n\Dictionary\Options;

use InvalidArgumentException;
use Magento\Framework\Component\ComponentRegistrar;

/**
 * Options resolver factory.
 */
class ResolverFactory
{
    /**
     * Default option resolver class.
     */
    public const DEFAULT_RESOLVER = Resolver::class;

    /**
     * @var string
     */
    protected $resolverClass;

    /**
     * @param string $resolverClass
     */
    public function __construct($resolverClass = self::DEFAULT_RESOLVER)
    {
        $this->resolverClass = $resolverClass;
    }

    /**
     * @param string $directory
     * @param bool $withContext
     *
     * @throws InvalidArgumentException
     *
     * @return ResolverInterface
     */
    public function create($directory, $withContext)
    {
        $resolver = new $this->resolverClass(new ComponentRegistrar, $directory, $withContext);

        if (! $resolver instanceof ResolverInterface) {
            throw new InvalidArgumentException($this->resolverClass . ' doesn\'t implement ResolverInterface');
        }

        return $resolver;
    }
}
