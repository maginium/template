<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Module\Di\Code\Scanner;

use Laminas\Code\Reflection\ClassReflection;
use Magento\Framework\Api\Code\Generator\ExtensionAttributesGenerator;
use Magento\Framework\Api\Code\Generator\ExtensionAttributesInterfaceGenerator;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManager\Code\Generator\Factory as FactoryGenerator;
use Magento\Framework\Reflection\TypeProcessor;
use Magento\Setup\Module\Di\Compiler\Log\Log;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * Finds factory and extension attributes classes which require auto-generation.
 */
class PhpScanner implements ScannerInterface
{
    /**
     * @var Log
     */
    protected $_log;

    /**
     * @var TypeProcessor
     */
    private $typeProcessor;

    /**
     * Initialize dependencies.
     *
     * @param Log $log
     * @param TypeProcessor|null $typeProcessor
     */
    public function __construct(Log $log, ?TypeProcessor $typeProcessor = null)
    {
        $this->_log = $log;
        $this->typeProcessor = $typeProcessor
            ?: ObjectManager::getInstance()->get(TypeProcessor::class);
    }

    /**
     * Get array of class names.
     *
     * @param array $files
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = [];

        foreach ($files as $file) {
            $classes = $this->getDeclaredClasses($file);

            foreach ($classes as $className) {
                $reflectionClass = new ReflectionClass($className);
                $output[] = $this->_fetchFactories($reflectionClass, $file);
                $output[] = $this->_fetchMissingExtensionAttributesClasses($reflectionClass, $file);
            }
        }

        return array_unique(array_merge([], ...$output));
    }

    /**
     * Identify source class name for the provided class.
     *
     * @param string $missingClassName
     * @param string $entityType
     *
     * @return string
     */
    protected function getSourceClassName($missingClassName, $entityType)
    {
        $sourceClassName = rtrim(mb_substr($missingClassName, 0, -mb_strlen($entityType)), '\\');
        $entityType = lcfirst($entityType);

        if ($entityType === ExtensionAttributesInterfaceGenerator::ENTITY_TYPE
            || $entityType === ExtensionAttributesGenerator::ENTITY_TYPE
        ) {
            // Process special cases for extension class and extension interface
            return $sourceClassName . 'Interface';
        }

        if ($entityType === FactoryGenerator::ENTITY_TYPE) {
            $extensionAttributesSuffix = ucfirst(ExtensionAttributesGenerator::ENTITY_TYPE);

            if (mb_substr($sourceClassName, -mb_strlen($extensionAttributesSuffix)) === $extensionAttributesSuffix) {
                /** Process special case for extension factories */
                $extensionAttributesClass = mb_substr(
                    $sourceClassName,
                    0,
                    -mb_strlen(ExtensionAttributesGenerator::ENTITY_TYPE),
                );
                $sourceClassName = $extensionAttributesClass . 'Interface';
            }
        }

        return $sourceClassName;
    }

    /**
     * Fetch factories from class constructor.
     *
     * @param ReflectionClass $reflectionClass
     * @param string $file
     *
     * @return string[]
     */
    protected function _fetchFactories($reflectionClass, $file)
    {
        $absentFactories = $this->findMissingFactories(
            $file,
            $reflectionClass,
            '__construct',
            ucfirst(FactoryGenerator::ENTITY_TYPE),
        );

        return $absentFactories;
    }

    /**
     * Find missing extension attributes related classes, interfaces and factories.
     *
     * @param ReflectionClass $reflectionClass
     * @param string $file
     *
     * @return string[]
     */
    protected function _fetchMissingExtensionAttributesClasses($reflectionClass, $file)
    {
        $missingExtensionInterfaces = [];
        $methodName = 'getExtensionAttributes';
        $entityType = ucfirst(ExtensionAttributesInterfaceGenerator::ENTITY_TYPE);

        if ($reflectionClass->hasMethod($methodName) && $reflectionClass->isInterface()) {
            $returnType = $this->typeProcessor->getGetterReturnType(
                (new ClassReflection($reflectionClass->getName()))->getMethod($methodName),
            );
            $missingClassName = $returnType['type'];

            if ($this->shouldGenerateClass($missingClassName, $entityType, $file)) {
                $missingExtensionInterfaces[] = $missingClassName;

                $extension = rtrim(mb_substr($missingClassName, 0, -mb_strlen('Interface')), '\\');

                if (! class_exists($extension)) {
                    $missingExtensionInterfaces[] = $extension;
                }
                $extensionFactory = $extension . 'Factory';

                if (! class_exists($extensionFactory)) {
                    $missingExtensionInterfaces[] = $extensionFactory;
                }
            }
        }

        return $missingExtensionInterfaces;
    }

    /**
     * Fetch namespaces from tokenized PHP file.
     *
     * @param int $tokenIterator
     * @param int $count
     * @param array $tokens
     *
     * @return string
     */
    protected function _fetchNamespace($tokenIterator, $count, $tokens)
    {
        $namespaceParts = [];

        for ($tokenOffset = $tokenIterator + 1; $tokenOffset < $count; $tokenOffset++) {
            if ($tokens[$tokenOffset][0] === T_NAME_QUALIFIED) {
                $namespaceParts[] = $tokens[$tokenOffset][1];
            }

            if ($tokens[$tokenOffset][0] === T_STRING) {
                $namespaceParts[] = '\\';
                $namespaceParts[] = $tokens[$tokenOffset][1];
            } elseif ($tokens[$tokenOffset] === '{' || $tokens[$tokenOffset] === ';') {
                break;
            }
        }

        return implode('', $namespaceParts);
    }

    /**
     * Find classes which are used as parameters types of the specified method and are not declared.
     *
     * @param string $file
     * @param ReflectionClass $classReflection
     * @param string $methodName
     * @param string $entityType
     *
     * @return string[]
     */
    private function findMissingFactories($file, $classReflection, $methodName, $entityType)
    {
        $missingClasses = [];

        if (! $classReflection->hasMethod($methodName)) {
            return $missingClasses;
        }

        $factorySuffix = '\\' . ucfirst(FactoryGenerator::ENTITY_TYPE);
        $constructor = $classReflection->getMethod($methodName);
        $parameters = $constructor->getParameters();

        /** @var $parameter \ReflectionParameter */
        foreach ($parameters as $parameter) {
            preg_match('/\[\s\<\w+?>\s\??([\w\\\\]+)/s', $parameter->__toString(), $matches);

            if (isset($matches[1]) && mb_substr($matches[1], -mb_strlen($entityType)) === $entityType) {
                $missingClassName = $matches[1];

                if ($this->shouldGenerateClass($missingClassName, $entityType, $file)) {
                    if (mb_substr($missingClassName, -mb_strlen($factorySuffix)) === $factorySuffix) {
                        $entityName = rtrim(mb_substr($missingClassName, 0, -mb_strlen($factorySuffix)), '\\');
                        $this->_log->add(
                            Log::CONFIGURATION_ERROR,
                            $missingClassName,
                            'Invalid Factory declaration for class ' . $entityName . ' in file ' . $file,
                        );
                    } else {
                        $missingClasses[] = $missingClassName;
                    }
                }
            }
        }

        return $missingClasses;
    }

    /**
     * Fetches class name from tokenized PHP file.
     *
     * @param string $namespace
     * @param int $tokenIterator
     * @param int $count
     * @param array $tokens
     *
     * @return string|null
     */
    private function fetchClass($namespace, $tokenIterator, $count, $tokens): ?string
    {
        // anonymous classes should be omitted
        if (is_array($tokens[$tokenIterator - 2]) && $tokens[$tokenIterator - 2][0] === T_NEW) {
            return null;
        }

        for ($tokenOffset = $tokenIterator + 1; $tokenOffset < $count; $tokenOffset++) {
            if ($tokens[$tokenOffset] !== '{') {
                continue;
            }

            return $namespace . '\\' . $tokens[$tokenIterator + 2][1];
        }

        return null;
    }

    /**
     * Get classes and interfaces declared in the file.
     *
     * @param string $file
     *
     * @return array
     */
    private function getDeclaredClasses($file): array
    {
        $classes = [];
        $namespaceParts = [];
        // phpcs:ignore
        $tokens = token_get_all(file_get_contents($file));
        $count = count($tokens);

        for ($tokenIterator = 0; $tokenIterator < $count; $tokenIterator++) {
            if ($tokens[$tokenIterator][0] === T_NAMESPACE) {
                $namespaceParts[] = $this->_fetchNamespace($tokenIterator, $count, $tokens);
            }

            if (($tokens[$tokenIterator][0] === T_CLASS || $tokens[$tokenIterator][0] === T_INTERFACE)
                && $tokens[$tokenIterator - 1][0] !== T_DOUBLE_COLON
            ) {
                $class = $this->fetchClass(implode('', $namespaceParts), $tokenIterator, $count, $tokens);

                if ($class !== null && ! in_array($class, $classes)) {
                    $classes[] = $class;
                }
            }
        }

        return $classes;
    }

    /**
     * Check if specified class is missing and if it can be generated.
     *
     * @param string $missingClassName
     * @param string $entityType
     * @param string $file
     *
     * @return bool
     */
    private function shouldGenerateClass($missingClassName, $entityType, $file)
    {
        try {
            if (class_exists($missingClassName)) {
                return false;
            }
        } catch (RuntimeException $e) { //phpcs:ignore
        }
        $sourceClassName = $this->getSourceClassName($missingClassName, $entityType);

        if (! class_exists($sourceClassName) && ! interface_exists($sourceClassName)) {
            $this->_log->add(
                Log::CONFIGURATION_ERROR,
                $missingClassName,
                "Invalid {$entityType} for nonexistent class {$sourceClassName} in file {$file}",
            );

            return false;
        }

        return true;
    }
}
