<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Di\Code\Reader\Decorator;

use Magento\Framework\Code\Reader\ClassReader;
use Magento\Framework\Code\Validator;
use Magento\Framework\Exception\ValidatorException;
use Magento\Setup\Module\Di\Code\Reader\ClassesScanner;
use Magento\Setup\Module\Di\Code\Reader\ClassesScannerInterface;
use Magento\Setup\Module\Di\Compiler\Log\Log;
use ReflectionException;

/**
 * Class Directory.
 */
class Directory implements ClassesScannerInterface
{
    /**
     * @var string
     */
    private $current;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var array
     */
    private $relations = [];

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var ClassReader
     */
    private $classReader;

    /**
     * @var ClassesScanner
     */
    private $classesScanner;

    /**
     * @var string
     */
    private $generationDir;

    /**
     * @param Log $log Logging object
     * @param ClassReader $classReader
     * @param ClassesScanner $classesScanner
     * @param Validator $validator
     * @param string $generationDir directory where generated files is
     */
    public function __construct(
        Log $log,
        ClassReader $classReader,
        ClassesScanner $classesScanner,
        Validator $validator,
        $generationDir,
    ) {
        $this->log = $log;
        $this->classReader = $classReader;
        $this->classesScanner = $classesScanner;
        $this->validator = $validator;
        $this->generationDir = $generationDir;

        set_error_handler([$this, 'errorHandler'], E_STRICT);
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
        foreach ($this->classesScanner->getList($path) as $className) {
            $this->current = $className; // for errorHandler function

            try {
                if ($path !== $this->generationDir) { // validate all classes except classes in generation dir
                    $this->validator->validate($className);
                }
                $this->relations[$className] = $this->classReader->getParents($className);
            } catch (ValidatorException $exception) {
                $this->log->add(Log::COMPILATION_ERROR, $className, $exception->getMessage());
            } catch (ReflectionException $e) {
                $this->log->add(Log::COMPILATION_ERROR, $className, $e->getMessage());
            }
        }

        return $this->relations;
    }

    /**
     * ErrorHandler for logging.
     *
     * @param int $errorNumber
     * @param string $msg
     *
     * @return void
     */
    public function errorHandler($errorNumber, $msg)
    {
        $this->log->add(Log::COMPILATION_ERROR, $this->current, '#' . $errorNumber . ' ' . $msg);
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
