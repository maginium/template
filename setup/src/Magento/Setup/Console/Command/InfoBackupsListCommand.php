<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Backup\Factory;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Setup\BackupRollback;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command prints list of available backup files.
 */
class InfoBackupsListCommand extends Command
{
    /**
     * File.
     *
     * @var File
     */
    private $file;

    /**
     * Filesystem Directory List.
     *
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var TableFactory
     */
    private $tableHelperFactory;

    /**
     * @param DirectoryList $directoryList
     * @param File $file
     * @param TableFactory $tableHelperFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file,
        ?TableFactory $tableHelperFactory = null,
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->tableHelperFactory = $tableHelperFactory ?: ObjectManager::getInstance()->create(TableFactory::class);
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $backupsDir = $this->directoryList->getPath(DirectoryList::VAR_DIR)
            . '/' . BackupRollback::DEFAULT_BACKUP_DIRECTORY;

        if ($this->file->isExists($backupsDir)) {
            $contents = $this->file->readDirectoryRecursively($backupsDir);
            $tempTable = [];

            foreach ($contents as $path) {
                $partsOfPath = explode('/', str_replace('\\', '/', $path));
                $fileName = $partsOfPath[count($partsOfPath) - 1];

                // if filename starts with '.' skip, e.g. '.git'
                if (! str_starts_with($fileName, '.')) {
                    $filenameWithoutExtension = explode('.', $fileName);
                    // actually first part of $filenameWithoutExtension contains only the filename without extension
                    // and filename contains the type of backup separated by '_'
                    $fileNameParts = explode('_', $filenameWithoutExtension[0]);

                    if (in_array(Factory::TYPE_MEDIA, $fileNameParts)) {
                        $tempTable[$fileName] = Factory::TYPE_MEDIA;
                    } elseif (in_array(Factory::TYPE_DB, $fileNameParts)) {
                        $tempTable[$fileName] = Factory::TYPE_DB;
                    } elseif (in_array('code', $fileNameParts)) {
                        $tempTable[$fileName] = 'code';
                    }
                }
            }

            if (empty($tempTable)) {
                $output->writeln('<info>No backup files found.</info>');

                return Cli::RETURN_SUCCESS;
            }
            $output->writeln("<info>Showing backup files in {$backupsDir}.</info>");

            /** @var Table $tableHelper */
            $tableHelper = $this->tableHelperFactory->create(['output' => $output]);
            $tableHelper->setHeaders(['Backup Filename', 'Backup Type']);
            asort($tempTable);

            foreach ($tempTable as $key => $value) {
                $tableHelper->addRow([$key, $value]);
            }
            $tableHelper->render();
        } else {
            $output->writeln('<info>No backup files found.</info>');
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('info:backups:list')
            ->setDescription('Prints list of available backup files');

        parent::configure();
    }
}
