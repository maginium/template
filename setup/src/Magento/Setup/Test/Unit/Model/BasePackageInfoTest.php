<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model;

use Magento\Framework\FileSystem\Directory\ReadFactory;
use Magento\Framework\FileSystem\Directory\ReadInterface;
use Magento\Setup\Exception;
use Magento\Setup\Model\BasePackageInfo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests BasePackageInfo.
 */
class BasePackageInfoTest extends TestCase
{
    /**
     * @var MockObject|ReadFactory
     */
    private $readFactoryMock;

    /**
     * @var MockObject|ReadInterface
     */
    private $readerMock;

    /**
     * @var MockObject|BasePackageInfo
     */
    private $basePackageInfo;

    // Error scenario: magento/magento2-base/composer.json not found
    /**
     * @test
     */
    public function baseComposerJsonFileNotFound()
    {
        $this->readerMock->expects($this->once())->method('isExist')->willReturn(false);
        $this->readerMock->expects($this->never())->method('isReadable');
        $this->readerMock->expects($this->never())->method('readFile');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            sprintf('Could not locate %s file.', BasePackageInfo::MAGENTO_BASE_PACKAGE_COMPOSER_JSON_FILE),
        );
        $this->basePackageInfo->getPaths();
    }

    // Error scenario: magento/magento2-base/composer.json file could not be read
    /**
     * @test
     */
    public function baseComposerJsonFileNotReadable()
    {
        $this->readerMock->expects($this->once())->method('isExist')->willReturn(true);
        $this->readerMock->expects($this->once())->method('isReadable')->willReturn(false);
        $this->readerMock->expects($this->never())->method('readFile');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            sprintf('Could not read %s file.', BasePackageInfo::MAGENTO_BASE_PACKAGE_COMPOSER_JSON_FILE),
        );
        $this->basePackageInfo->getPaths();
    }

    // Scenario: ["extra"]["map"] is absent within magento/magento2-base/composer.json file
    /**
     * @test
     */
    public function baseNoExtraMapSectionInComposerJsonFile()
    {
        $this->readerMock->expects($this->once())->method('isExist')->willReturn(true);
        $this->readerMock->expects($this->once())->method('isReadable')->willReturn(true);
        $jsonData = json_encode(
            [
                BasePackageInfo::COMPOSER_KEY_EXTRA => [
                    __FILE__,
                    __FILE__,
                ],
            ],
        );
        $this->readerMock->expects($this->once())->method('readFile')->willReturn($jsonData);
        $expectedList = [];
        $actualList = $this->basePackageInfo->getPaths();
        $this->assertEquals($expectedList, $actualList);
    }

    // Success scenario
    /**
     * @test
     */
    public function basePackageInfo()
    {
        $this->readerMock->expects($this->once())->method('isExist')->willReturn(true);
        $this->readerMock->expects($this->once())->method('isReadable')->willReturn(true);
        $jsonData = json_encode(
            [
                BasePackageInfo::COMPOSER_KEY_EXTRA => [
                    BasePackageInfo::COMPOSER_KEY_MAP => [
                        [
                            __FILE__,
                            __FILE__,
                        ],
                        [
                            __DIR__,
                            __DIR__,
                        ],
                    ],
                ],
            ],
        );
        $this->readerMock->expects($this->once())->method('readFile')->willReturn($jsonData);
        $expectedList = [__FILE__, __DIR__];
        $actualList = $this->basePackageInfo->getPaths();
        $this->assertEquals($expectedList, $actualList);
    }

    protected function setup(): void
    {
        $this->readFactoryMock = $this->createMock(ReadFactory::class);
        $this->readerMock = $this->getMockForAbstractClass(
            ReadInterface::class,
            [],
            '',
            false,
        );
        $this->readFactoryMock->expects($this->once())->method('create')->willReturn($this->readerMock);
        $this->basePackageInfo = new BasePackageInfo($this->readFactoryMock);
    }
}
