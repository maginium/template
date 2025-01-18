<?php

/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\Data\ConfigData;
use Magento\Framework\Config\Data\ConfigDataFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Setup\Model\ConfigGenerator;
use Magento\Setup\Model\ConfigOptionsList\DriverOptions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Setup\Model\ConfigGenerator class.
 */
class ConfigGeneratorTest extends TestCase
{
    /**
     * @var DeploymentConfig|MockObject
     */
    private $deploymentConfigMock;

    /**
     * @var ConfigGenerator|MockObject
     */
    private $model;

    /**
     * @var ConfigData|MockObject
     */
    private $configDataMock;

    /**
     * @var DriverOptions
     */
    private $driverOptionsMock;

    /**
     * @test
     */
    public function createXFrameConfig()
    {
        $this->deploymentConfigMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(ConfigOptionsListConstants::CONFIG_PATH_X_FRAME_OPT)
            ->willReturn(null);

        $this->configDataMock
            ->expects($this->once())
            ->method('set')
            ->with(ConfigOptionsListConstants::CONFIG_PATH_X_FRAME_OPT, 'SAMEORIGIN');

        $this->model->createXFrameConfig();
    }

    /**
     * @test
     */
    public function createCacheHostsConfig()
    {
        $data = [ConfigOptionsListConstants::INPUT_KEY_CACHE_HOSTS => 'localhost:8080, website.com, 120.0.0.1:90'];
        $expectedData = [
            0 => [
                'host' => 'localhost',
                'port' => '8080',
            ],
            1 => [
                'host' => 'website.com',
            ],
            2 => [
                'host' => '120.0.0.1',
                'port' => '90',
            ],
        ];

        $this->configDataMock
            ->expects($this->once())
            ->method('set')
            ->with(ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS, $expectedData);

        $this->model->createCacheHostsConfig($data);
    }

    /**
     * @test
     */
    public function createModeConfig()
    {
        $this->deploymentConfigMock->expects($this->once())
            ->method('get')
            ->with(State::PARAM_MODE)
            ->willReturn(null);

        $this->configDataMock
            ->expects($this->once())
            ->method('set')
            ->with(State::PARAM_MODE, State::MODE_DEFAULT);

        $this->model->createModeConfig();
    }

    /**
     * @test
     */
    public function createModeConfigIfAlreadySet()
    {
        $this->deploymentConfigMock->expects($this->once())
            ->method('get')
            ->with(State::PARAM_MODE)
            ->willReturn(State::MODE_PRODUCTION);
        $configData = $this->model->createModeConfig();
        $this->assertSame([], $configData->getData());
    }

    /**
     * @test
     */
    public function createCryptKeyConfig()
    {
        $key = 'my-new-key';
        $data = [ConfigOptionsListConstants::INPUT_KEY_ENCRYPTION_KEY => $key];

        $this->deploymentConfigMock
            ->method('get')
            ->with(ConfigOptionsListConstants::CONFIG_PATH_CRYPT_KEY)
            ->willReturn(null);

        $this->configDataMock
            ->expects($this->once())
            ->method('set')
            ->with(ConfigOptionsListConstants::CONFIG_PATH_CRYPT_KEY, $key);

        $this->model->createCryptConfig($data);
    }

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->deploymentConfigMock = $this->getMockBuilder(DeploymentConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configDataMock = $this->getMockBuilder(ConfigData::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['set'])
            ->getMock();

        $configDataFactoryMock = $this->getMockBuilder(ConfigDataFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $configDataFactoryMock->method('create')
            ->willReturn($this->configDataMock);

        $this->driverOptionsMock = $this->getMockBuilder(DriverOptions::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDriverOptions'])
            ->getMock();

        $this->model = $objectManager->getObject(
            ConfigGenerator::class,
            [
                'deploymentConfig' => $this->deploymentConfigMock,
                'configDataFactory' => $configDataFactoryMock,
                'driverOptions' => $this->driverOptionsMock,
            ],
        );
    }
}
