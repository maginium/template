<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Definition;

use Magento\Setup\Module\Di\Definition\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * Instance name.
     */
    public const INSTANCE_1 = 'Class_Name_1';

    /**
     * Instance name.
     */
    public const INSTANCE_2 = 'Class_Name_2';

    /**
     * @var Collection
     */
    private $model;

    /**
     * @var Collection|MockObject
     */
    private $collectionMock;

    /**
     * @test
     */
    public function addDefinition()
    {
        $this->model->addDefinition(self::INSTANCE_1, $this->getArgument());
        $this->assertEquals($this->getExpectedDefinition(), $this->model->getCollection());
    }

    /**
     * @test
     */
    public function initialize()
    {
        $this->model->initialize([self::INSTANCE_1 => $this->getArgument()]);
        $this->assertEquals($this->getExpectedDefinition(), $this->model->getCollection());
    }

    /**
     * @test
     */
    public function hasInstance()
    {
        $this->model->addDefinition(self::INSTANCE_1, $this->getArgument());
        $this->assertTrue($this->model->hasInstance(self::INSTANCE_1));
    }

    /**
     * @test
     */
    public function getInstancesNamesList()
    {
        $this->model->addDefinition(self::INSTANCE_1, $this->getArgument());
        $this->assertEquals([self::INSTANCE_1], $this->model->getInstancesNamesList());
    }

    /**
     * @test
     */
    public function getInstanceArguments()
    {
        $this->model->addDefinition(self::INSTANCE_1, $this->getArgument());
        $this->assertEquals($this->getArgument(), $this->model->getInstanceArguments(self::INSTANCE_1));
    }

    /**
     * @test
     */
    public function addCollection()
    {
        $this->model->addDefinition(self::INSTANCE_1, $this->getArgument());
        $this->collectionMock->expects($this->any())->method('getCollection')
            ->willReturn([self::INSTANCE_2 => $this->getArgument()]);
        $this->model->addCollection($this->collectionMock);
        $this->assertEquals(
            [self::INSTANCE_1 => $this->getArgument(), self::INSTANCE_2 => $this->getArgument()],
            $this->model->getCollection(),
        );
    }

    protected function setUp(): void
    {
        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->getMock();
        $this->model = new Collection;
    }

    /**
     * Returns initialized argument data.
     *
     * @return array
     */
    private function getArgument()
    {
        return ['argument' => ['configuration', 'array', true, null]];
    }

    /**
     * Returns initialized expected definitions for most cases.
     *
     * @return array
     */
    private function getExpectedDefinition()
    {
        return [self::INSTANCE_1 => $this->getArgument()];
    }
}
