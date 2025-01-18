<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Code\Reader;

use Magento\Setup\Module\Di\Code\Reader\FileClassScanner;
use Magento\Setup\Module\Di\Code\Reader\InvalidFileException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileClassScannerTest extends TestCase
{
    /**
     * @test
     */
    public function invalidFileThrowsException()
    {
        $this->expectException(InvalidFileException::class);
        new FileClassScanner('');
    }

    /**
     * @test
     */
    public function emptyArrayForFileWithoutNamespaceOrClass()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<'PHP'
<?php

echo 'hello world';

if (class_exists('some_class')) {
    $object = new some_class();
}
PHP
            );

        /** @var $scanner FileClassScanner */
        $result = $scanner->getClassName();
        self::assertEmpty($result);
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<'PHP'
<?php

class ThisIsATest {

}
PHP
            );

        /** @var $scanner FileClassScanner */
        $result = $scanner->getClassName();
        self::assertEquals('ThisIsATest', $result);
    }

    /**
     * @test
     */
    public function getClassNameAndSingleNamespace()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<'PHP'
<?php

namespace NS;

class ThisIsMyTest {

}
PHP
            );

        /** @var $scanner FileClassScanner */
        $result = $scanner->getClassName();
        self::assertEquals('NS\ThisIsMyTest', $result);
    }

    /**
     * @test
     */
    public function getClassNameAndMultiNamespace()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<PHP
<?php

namespace This\Is\My\Ns;

class ThisIsMyTest {

    public function __construct()
    {
        \This\Is\Another\Ns::class;
    }

    public function test()
    {

    }
}
PHP
            );

        /** @var $scanner FileClassScanner */
        $result = $scanner->getClassName();
        self::assertEquals('This\Is\My\Ns\ThisIsMyTest', $result);
    }

    /**
     * @test
     */
    public function getMultiClassNameAndMultiNamespace()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<PHP
<?php

namespace This\Is\My\Ns;

class ThisIsMyTest {

    public function __construct()
    {
        \$this->get(\This\Is\Another\Ns::class)->method();
        self:: class;
    }

    public function test()
    {

    }
}

class ThisIsForBreaking {

}

PHP
            );

        /** @var $scanner FileClassScanner */
        $result = $scanner->getClassName();
        // only a single class should be in the file
        self::assertEquals('This\Is\My\Ns\ThisIsMyTest', $result);
    }

    /**
     * @test
     */
    public function bracketedNamespacesAndClasses()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<PHP
<?php

namespace This\Is\My\Ns {

    class ThisIsMyTest
    {

        public function __construct()
        {
            \This\Is\Another\Ns::class;
            self:: class;
        }

    }

    class ThisIsForBreaking
    {
    }
}

namespace This\Is\Not\My\Ns {

    class ThisIsNotMyTest
    {
    }
}

PHP
            );

        /** @var $scanner FileClassScanner */
        $result = $scanner->getClassName();
        // only a single class should in the file
        self::assertEquals('This\Is\My\Ns\ThisIsMyTest', $result);
    }

    /**
     * @test
     */
    public function multipleClassKeywordsInMiddleOfFileWithStringVariableParsing()
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<'PHP'
<?php

namespace This\Is\My\Ns;

use stdClass;

class ThisIsMyTest
{
    protected function firstMethod()
    {
        $test = 1;
        $testString = "foo {$test}";
        $className = stdClass::class;
        $testString2 = "bar {$test}";
    }

    protected function secondMethod()
    {
        $this->doMethod(stdClass::class)->runAction();
    }
}

PHP
            );

        // @var $scanner FileClassScanner
        $result = $scanner->getClassName();
        self::assertEquals('This\Is\My\Ns\ThisIsMyTest', $result);
    }

    /**
     * @test
     */
    public function invalidPHPCodeThrowsExceptionWhenCannotDetermineBraceOrSemiColon()
    {
        $this->expectException(InvalidFileException::class);
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<PHP
            <?php

namespace This\Is\My\Ns

class ThisIsMyTest
{
}

PHP
            );

        /** @var $scanner FileClassScanner */
        $scanner->getClassName();
    }

    /**
     * Checks a case when file with class also contains `class_alias` function for backward compatibility.
     *
     * @test
     */
    public function fileContainsClassAliasFunction(): void
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<'PHP'
<?php

namespace This\Is\My\Ns;

use stdClass;

class ThisIsMyTest
{
    public function doMethod()
    {
        $className = stdClass::class;
        return $className;
    }

    public function secondMethod()
    {
        $this->doMethod();
    }
}

class_alias(\This\Is\My\Ns\ThisIsMyTest::class, stdClass::class);

PHP
            );

        // @var $scanner FileClassScanner
        $result = $scanner->getClassName();
        self::assertEquals('This\Is\My\Ns\ThisIsMyTest', $result);
    }

    /**
     * Checks a case when file with class also contains `class_exists` function.
     *
     * @test
     */
    public function fileContainsClassExistsFunction(): void
    {
        $scanner = $this->getScannerMockObject();
        $scanner->expects(self::once())
            ->method('getFileContents')
            ->willReturn(
                <<<PHP
<?php

namespace This\Is\My\Ns;

if (false) {
    class ThisIsMyTest {}
}

class_exists(\This\Is\My\Ns\ThisIsMySecondTest::class);
trigger_error('This class is does not supported');
PHP
            );

        // @var $scanner FileClassScanner
        $result = $scanner->getClassName();
        self::assertEmpty($result);
    }

    /**
     * Creates file class scanner mock object.
     *
     * @return MockObject
     */
    private function getScannerMockObject(): MockObject
    {
        $scanner = $this->getMockBuilder(FileClassScanner::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFileContents'])
            ->getMock();

        return $scanner;
    }
}
