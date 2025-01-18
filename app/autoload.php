<?php

declare(strict_types=1);

use Magento\Framework\Autoload\AutoloaderRegistry;
use Magento\Framework\Autoload\ClassLoaderWrapper;

/*
|--------------------------------------------------------------------------
| Define Autoload Constants
|--------------------------------------------------------------------------
|
| This section defines various constants for the autoload. These constants
| are used globally within the app, ensuring consistency and easy management.
|
*/

// Define the root directory of the application
defined('BP') || define('BP', dirname(__DIR__));

// Define the path to the vendor directory
defined('VENDOR_PATH') || define('VENDOR_PATH', BP . '/vendor');

/*
|--------------------------------------------------------------------------
| Ensure Vendor Path is Readable
|--------------------------------------------------------------------------
|
| Verify that the vendor path directory is readable. If not, an exception
| is thrown indicating that there might be an issue with file permissions.
| This check ensures that subsequent operations involving the vendor directory
| will not fail due to accessibility issues.
|
*/
if (! is_readable(VENDOR_PATH)) {
    throw new \Exception(
        'The vendor path file is not readable. This usually indicates incorrect file permissions.',
    );
}

/**
 * Retrieves the path to the vendor autoload file.
 *
 * This function attempts to locate the vendor autoload file based on the path provided
 * in the vendor directory. It checks the following:
 * 1. The standard path within the vendor directory specified by the `VENDOR_PATH`.
 * 2. A fallback path if the autoload file is not found in the primary location.
 *
 * @param string $vendorDir The vendor directory path.
 * @param string $autoloadFile The filename of the autoload file.
 *
 * @return string|null The path to the vendor autoload file, or null if not found.
 */
$findAutoloadFile = function(string $vendorDir, string $autoloadFile): ?string {
    // Construct the path to the autoload file
    $autoloadPath = joinPaths($vendorDir, $autoloadFile);

    // Check if the autoload file exists and is readable
    return is_readable($autoloadPath) ? $autoloadPath : null;
};

/*
|--------------------------------------------------------------------------
| Handle Missing Autoload File
|--------------------------------------------------------------------------
|
| If the vendor autoload file cannot be found, an exception is thrown. This
| ensures that the application halts with a clear message if Composer's
| autoload file is missing, indicating the need to run `composer install`
| in the application root directory.
|
*/
$vendorAutoload = $findAutoloadFile(VENDOR_PATH, 'autoload.php');

// If no autoload file is found, throw an exception
if ($vendorAutoload === null) {
    throw new \Exception(
        'Vendor autoload file not found. Please run `composer install` in the application root directory.',
    );
}

// Include the vendor autoload file and register the autoloader
$composerAutoloader = include $vendorAutoload;

/*
|--------------------------------------------------------------------------
| Custom Autoloader Registration
|--------------------------------------------------------------------------
|
| This step registers the Composer autoloader with a custom autoloader wrapper
| and registers it within the AutoloaderRegistry. It allows for enhanced control
| over the autoloading mechanism, supporting additional class loading logic
| and module management.
|
*/
$classLoadWrapper = new ClassLoaderWrapper($composerAutoloader);
AutoloaderRegistry::registerAutoloader($classLoadWrapper);
