<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Setup;

use Magento\Setup\Module\Setup\SetupCache;
use PHPUnit\Framework\TestCase;
use stdClass;

class SetupCacheTest extends TestCase
{
    /**
     * @var SetupCache
     */
    private $object;

    /**
     * @test
     */
    public function remove()
    {
        $table = 'table';
        $parentId = 'parent';
        $rowId = 'row';
        $data = new stdClass;

        $this->object->setRow($table, $parentId, $rowId, $data);
        $this->object->remove($table, $parentId, $rowId, $data);
        $this->assertFalse($this->object->get($table, $parentId, $rowId));
    }

    /**
     * @test
     */
    public function setRow()
    {
        $table = 'table';
        $parentId = 'parent';
        $rowId = 'row';
        $data = new stdClass;

        $this->object->setRow($table, $parentId, $rowId, $data);
        $this->assertSame($data, $this->object->get($table, $parentId, $rowId));
    }

    /**
     * @test
     */
    public function setField()
    {
        $table = 'table';
        $parentId = 'parent';
        $rowId = 'row';
        $field = 'field';
        $data = new stdClass;

        $this->object->setField($table, $parentId, $rowId, $field, $data);
        $this->assertSame($data, $this->object->get($table, $parentId, $rowId, $field));
    }

    /**
     * @dataProvider getNonexistentDataProvider
     *
     * @param string $field
     *
     * @test
     */
    public function getNonexistent($field)
    {
        $this->assertFalse($this->object->get('table', 'parent', 'row', $field));
    }

    /**
     * @return array
     */
    public function getNonexistentDataProvider()
    {
        return [
            [null],
            ['field'],
        ];
    }

    /**
     * @dataProvider hasDataProvider
     *
     * @param string $table
     * @param string $parentId
     * @param string $rowId
     * @param string $field
     * @param bool $expected
     *
     * @test
     */
    public function has($table, $parentId, $rowId, $field, $expected)
    {
        $this->object->setField('table', 'parent', 'row', 'field', 'data');
        $this->assertSame($expected, $this->object->has($table, $parentId, $rowId, $field));
    }

    /**
     * @return array
     */
    public function hasDataProvider()
    {
        return [
            'existing' => ['table', 'parent', 'row', 'field', true],
            'nonexistent field' => ['table', 'parent', 'row', 'other_field', false],
            'nonexistent row' => ['table', 'parent', 'other_row', 'field', false],
            'nonexistent parent' => ['table', 'other_parent', 'row', 'field', false],
            'nonexistent table' => ['other_table', 'parent', 'row', 'field', false],
        ];
    }

    protected function setUp(): void
    {
        $this->object = new SetupCache;
    }
}
