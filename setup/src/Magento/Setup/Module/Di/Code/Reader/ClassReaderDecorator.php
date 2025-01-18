<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Reader;

use Magento\Framework\Code\Reader\ClassReader;
use Magento\Framework\Code\Reader\ClassReaderInterface;
use Magento\Setup\Module\Di\Compiler\ConstructorArgument;
use ReflectionException;

class ClassReaderDecorator implements ClassReaderInterface
{
    /**
     * @var ClassReader
     */
    private $classReader;

    /**
     * @param ClassReader $classReader
     */
    public function __construct(ClassReader $classReader)
    {
        $this->classReader = $classReader;
    }

    /**
     * Read class constructor signature.
     *
     * @param string $className
     *
     * @throws ReflectionException
     *
     * @return ConstructorArgument[]|null
     */
    public function getConstructor($className)
    {
        $unmappedArguments = $this->classReader->getConstructor($className);

        if ($unmappedArguments === null) {
            return $unmappedArguments;
        }

        $arguments = [];

        foreach ($unmappedArguments as $argument) {
            $arguments[] = new ConstructorArgument($argument);
        }

        return $arguments;
    }

    /**
     * Retrieve parent relation information for type in a following format
     * array(
     *     'Parent_Class_Name',
     *     'Interface_1',
     *     'Interface_2',
     *     ...
     * ).
     *
     * @param string $className
     *
     * @return string[]
     */
    public function getParents($className)
    {
        return $this->classReader->getParents($className);
    }
}
