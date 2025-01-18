<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Reader\Decorator;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Code\Reader\ClassReader;
use Magento\Framework\Code\Validator;
use Magento\Framework\Code\Validator\ConstructorIntegrity;
use Magento\Framework\Exception\ValidatorException;
use Magento\Setup\Module\Di\Code\Reader\ClassesScanner;
use Magento\Setup\Module\Di\Code\Reader\ClassesScannerInterface;
use Magento\Setup\Module\Di\Code\Reader\ClassReaderDecorator;
use Magento\Setup\Module\Di\Compiler\Log\Log;
use ReflectionException;

/**
 * Class Interceptions.
 */
class Interceptions implements ClassesScannerInterface
{
    /**
     * @var ClassReaderDecorator
     */
    private $classReader;

    /**
     * @var ClassesScanner
     */
    private $classesScanner;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param ClassesScanner $classesScanner
     * @param ClassReader $classReader
     * @param Validator $validator
     * @param ConstructorIntegrity $constructorIntegrityValidator
     * @param Log $log
     */
    public function __construct(
        ClassesScanner $classesScanner,
        ClassReader $classReader,
        Validator $validator,
        ConstructorIntegrity $constructorIntegrityValidator,
        Log $log,
    ) {
        $this->classReader = $classReader;
        $this->classesScanner = $classesScanner;
        $this->validator = $validator;
        $this->log = $log;

        $this->validator->add($constructorIntegrityValidator);
    }

    /**
     * Retrieves list of classes for given path.
     *
     * @param string $path path to dir with files
     *
     * @return array
     */
    public function getList($path)
    {
        $nameList = [];

        foreach ($this->classesScanner->getList($path) as $className) {
            try {
                // validate all classes except classes in generated/code dir
                $generatedCodeDir = DirectoryList::getDefaultConfig()[DirectoryList::GENERATED_CODE];

                if (! str_contains($path, $generatedCodeDir[DirectoryList::PATH])) {
                    $this->validator->validate($className);
                }
                $nameList[] = $className;
            } catch (ValidatorException $exception) {
                $this->log->add(Log::COMPILATION_ERROR, $className, $exception->getMessage());
            } catch (ReflectionException $e) {
                $this->log->add(Log::COMPILATION_ERROR, $className, $e->getMessage());
            }
        }

        $this->log->report();

        return $nameList;
    }
}
