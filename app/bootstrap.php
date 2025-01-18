<?php

declare(strict_types=1);

use Magento\Framework\App\Bootstrap;
use Magento\Framework\Profiler;

/*
|--------------------------------------------------------------------------
| Enable Strict Error Reporting
|--------------------------------------------------------------------------
|
| Enable strict error reporting to ensure that all PHP errors, warnings,
| and notices are displayed during the development process for debugging
| purposes.
|
*/
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Define Bootstrap Constants
|--------------------------------------------------------------------------
|
| This section defines various constants for the application. These constants
| are used globally within the app, ensuring consistency and easy management.
|
*/
defined('SP') || define('SP', DIRECTORY_SEPARATOR);

/*
|--------------------------------------------------------------------------
| Unregister Phar Stream Wrapper
|--------------------------------------------------------------------------
|
| If the 'phar' stream wrapper is registered in the system, it will be
| unregistered. This is a security measure to prevent the use of Phar files
| in environments where such usage is restricted or disallowed.
|
*/
in_array('phar', stream_get_wrappers(), true) && stream_wrapper_unregister('phar');

/*
|--------------------------------------------------------------------------
| PHP Version Validation
|--------------------------------------------------------------------------
|
| Ensure that the application runs on PHP 8.1 or higher. If the PHP version
| is incompatible, an appropriate error message is displayed based on whether
| the script is being executed via CLI or the web server, and the application
| exits with a 503 status code indicating service unavailability.
|
*/
if (! defined('PHP_VERSION_ID') || PHP_VERSION_ID < 80100) {
    // Check if PHP version is less than 8.1.0
    $errorMessage = 'Maginium supports PHP 8.1.0 or later. Please read ' .

    // Error message to display
                    'https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/system-requirements.html';

    if (PHP_SAPI === 'cli') { // Check if script is executed from the command line interface
        // Output the error message to the CLI
        echo $errorMessage;
    } else {
        // Output an HTML error message for web-based requests
        echo <<<'HTML'
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <p>Maginium supports PHP 8.1.0 or later. Please read
    <a target="_blank" href="https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/system-requirements.html">
    Maginium System Requirements</a>.
</div>
HTML;
    }

    // Send an HTTP 503 response code indicating service unavailable
    http_response_code(503);

    // Exit the script with failure status
    exit(1);
}

/*
|--------------------------------------------------------------------------
| Define PHP Compatibility Constants
|--------------------------------------------------------------------------
|
| Define constants required by Maginium for PHP versions earlier than 8.0.
| These constants are necessary for maintaining backward compatibility.
|
*/
if (! defined('PHP_VERSION_ID') || PHP_VERSION_ID < 80000) { // Check if PHP version is less than 8.0
    // Define constant for qualified name if not defined
    defined('T_NAME_QUALIFIED') || define('T_NAME_QUALIFIED', 24001);

    // Define constant for fully qualified name if not defined
    defined('T_NAME_FULLY_QUALIFIED') || define('T_NAME_FULLY_QUALIFIED', 24002);
}

/*
|--------------------------------------------------------------------------
| Autoloader Initialization
|--------------------------------------------------------------------------
|
| Include the autoload file to load necessary classes and initialize
| dependencies. The Bootstrap class is then used to populate default
| autoloader mappings, which can be customized as needed.
|
*/

// Include the autoload file to handle class autoloading
require_once join_paths(__DIR__, 'autoload.php');

// Populate the autoloader with default mappings from the Magento bootstrap
Bootstrap::populateAutoloader(BP, []);

/*
|--------------------------------------------------------------------------
| Configure Umask for File Permissions
|--------------------------------------------------------------------------
|
| The umask value is set to control default file and directory permissions.
| If a `maginium_umask` file exists, its value is used. Otherwise, a default
| value of 002 is applied to ensure proper permissions.
|
*/

// Define the path to the umask configuration file
$umaskFile = join_paths(BP, 'maginium_umask');

// Set the umask based on the file contents or use a default value of 002
umask(file_exists($umaskFile) ? octdec(file_get_contents($umaskFile)) : 002);

/*
|--------------------------------------------------------------------------
| Remove IIS-specific Headers
|--------------------------------------------------------------------------
|
| For environments using IIS without URL rewrites, certain IIS-specific
| headers are removed to prevent conflicts and ensure proper URL handling.
|
*/
if (empty($_SERVER['ENABLE_IIS_REWRITES']) || ($_SERVER['ENABLE_IIS_REWRITES'] !== '1')) {
    unset(
        $_SERVER['HTTP_X_REWRITE_URL'], // Unset IIS rewrite URL.
        $_SERVER['HTTP_X_ORIGINAL_URL'], // Unset original URL header.
        $_SERVER['IIS_WasUrlRewritten'], // Unset the IIS rewrite flag.
        $_SERVER['UNENCODED_URL'], // Unset the unencoded URL header.
        $_SERVER['ORIG_PATH_INFO'], // Unset the original path info header.
    );
}

/*
|--------------------------------------------------------------------------
| Configure Maginium Profiler
|--------------------------------------------------------------------------
|
| Enable and configure the Maginium profiler if profiling is enabled through
| an environment variable or the `profiler.flag` file. Profiling is applied
| only to HTTP requests that accept HTML responses.
|
*/
if ((! empty($_SERVER['MAGE_PROFILER']) || file_exists(BP . '/var/profiler.flag')) && // Check if profiling is enabled through environment variable or profiler.flag file
    isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'text/html')) { // Check if the request accepts HTML response
    // Get the profiler configuration from environment or file
    $profilerConfig = $_SERVER['MAGE_PROFILER'] ?? trim(file_get_contents(BP . '/var/profiler.flag'));

    // Decode the JSON configuration or fallback to raw value
    $profilerConfig = json_decode($profilerConfig, true) ?: $profilerConfig;

    Profiler::applyConfig( // Apply the profiler configuration
        $profilerConfig,
        BP,
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest', // Check if the request is an AJAX request
    );
}

/*
|--------------------------------------------------------------------------
| Set Default Timezone and Floating-Point Precision
|--------------------------------------------------------------------------
|
| Set the default timezone to UTC to ensure consistency in date and time operations.
| Also, set the precision for floating-point numbers and serialized data.
|
*/
date_default_timezone_set('UTC');
ini_set('precision', '14');
ini_set('serialize_precision', '14');
