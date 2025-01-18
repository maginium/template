<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Console\Command;

use Magento\Framework\Setup\Lists;
use Magento\Setup\Console\Command\InfoTimezoneListCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableFactory;
use Symfony\Component\Console\Tester\CommandTester;

class InfoTimezoneListCommandTest extends TestCase
{
    /**
     * @test
     */
    public function execute()
    {
        $timezones = [
            'timezone' => 'timezone description',
        ];

        $table = $this->createMock(Table::class);
        $table->expects($this->once())->method('setHeaders')->with(['Timezone', 'Code']);
        $table->expects($this->once())->method('addRow')->with(['timezone description', 'timezone']);

        /** @var TableFactory|MockObject $helperSet */
        $tableFactoryMock = $this->createMock(TableFactory::class);
        $tableFactoryMock->expects($this->once())->method('create')->willReturn($table);

        /** @var Lists|MockObject $list */
        $list = $this->createMock(Lists::class);
        $list->expects($this->once())->method('getTimezoneList')->willReturn($timezones);
        $command = new InfoTimezoneListCommand($list, $tableFactoryMock);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
