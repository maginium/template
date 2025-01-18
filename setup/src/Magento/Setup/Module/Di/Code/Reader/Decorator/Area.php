<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Reader\Decorator;

use Magento\Framework\Exception\FileSystemException;
use Magento\Setup\Module\Di\Code\Reader\ClassesScanner;
use Magento\Setup\Module\Di\Code\Reader\ClassesScannerInterface;
use Magento\Setup\Module\Di\Code\Reader\ClassReaderDecorator;

/**
 * Class Area.
 */
class Area implements ClassesScannerInterface
{
    /**
     * @var ClassReaderDecorator
     */
    private $classReaderDecorator;

    /**
     * @var ClassesScanner
     */
    private $classesScanner;

    /**
     * @param ClassesScanner $classesScanner
     * @param ClassReaderDecorator $classReaderDecorator
     */
    public function __construct(
        ClassesScanner $classesScanner,
        ClassReaderDecorator $classReaderDecorator,
    ) {
        $this->classReaderDecorator = $classReaderDecorator;
        $this->classesScanner = $classesScanner;
    }

    /**
     * Retrieves list of classes for given path.
     *
     * @param string $path path to dir with files
     *
     * @throws FileSystemException
     *
     * @return array
     */
    public function getList($path)
    {
        $classes = [];

        foreach ($this->classesScanner->getList($path) as $className) {
            $classes[$className] = $this->classReaderDecorator->getConstructor($className);
        }

        return $classes;
    }
}
