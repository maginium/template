<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Module\Dependency\Report\Writer\Csv;

use InvalidArgumentException;
use Magento\Framework\File\Csv;
use Magento\Setup\Module\Dependency\Report\Data\ConfigInterface;
use Magento\Setup\Module\Dependency\Report\WriterInterface;

/**
 * Abstract csv file writer for reports.
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * Csv write object.
     *
     * @var Csv
     */
    protected $writer;

    /**
     * Writer constructor.
     *
     * @param Csv $writer
     */
    public function __construct($writer)
    {
        $this->writer = $writer;
    }

    /**
     * Template method. Main algorithm.
     *
     * {@inheritdoc}
     */
    public function write(array $options, ConfigInterface $config)
    {
        $this->checkOptions($options);

        $this->writeToFile($options['report_filename'], $this->prepareData($config));
    }

    /**
     * Template method. Check passed options step.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    protected function checkOptions($options)
    {
        if (! isset($options['report_filename']) || empty($options['report_filename'])) {
            throw new InvalidArgumentException('Writing error: Passed option "report_filename" is wrong.');
        }
    }

    /**
     * Template method. Prepare data step.
     *
     * @param ConfigInterface $config
     *
     * @return array
     */
    abstract protected function prepareData($config);

    /**
     * Template method. Write to file step.
     *
     * @param string $filename
     * @param array $data
     *
     * @return void
     */
    protected function writeToFile($filename, $data)
    {
        $this->writer->saveData($filename, $data);
    }
}
