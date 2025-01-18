<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Dependency\Parser;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Setup\Module\Dependency\Parser\Code;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
{
    /**
     * @var Code
     */
    protected $parser;

    /**
     * @param array $options
     *
     * @dataProvider dataProviderWrongOptionFilesForParse
     *
     * @test
     */
    public function parseWithWrongOptionFilesForParse($options)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Parse error: Option "files_for_parse" is wrong.');
        $this->parser->parse($options);
    }

    /**
     * @return array
     */
    public function dataProviderWrongOptionFilesForParse()
    {
        return [
            [['files_for_parse' => [], 'declared_namespaces' => [1, 2]]],
            [['files_for_parse' => 'sting', 'declared_namespaces' => [1, 2]]],
            [['there_are_no_files_for_parse' => [1, 3], 'declared_namespaces' => [1, 2]]],
        ];
    }

    /**
     * @param array $options
     *
     * @dataProvider dataProviderWrongOptionDeclaredNamespace
     *
     * @test
     */
    public function parseWithWrongOptionDeclaredNamespace($options)
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Parse error: Option "declared_namespaces" is wrong.');
        $this->parser->parse($options);
    }

    /**
     * @return array
     */
    public function dataProviderWrongOptionDeclaredNamespace()
    {
        return [
            [['declared_namespaces' => [], 'files_for_parse' => [1, 2]]],
            [['declared_namespaces' => 'sting', 'files_for_parse' => [1, 2]]],
            [['there_are_no_declared_namespaces' => [1, 3], 'files_for_parse' => [1, 2]]],
        ];
    }

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->parser = $objectManagerHelper->getObject(Code::class);
    }
}
