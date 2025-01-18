<?php

declare(strict_types=1);

use Magento\Framework\Error\ProcessorFactory;

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require_once 'processorFactory.php';

$processorFactory = new ProcessorFactory;
$processor = $processorFactory->createProcessor();
$response = $processor->process503();
$response->sendResponse();
