<?php

declare(strict_types=1);

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
| Glob Patterns for Registration Includes
|--------------------------------------------------------------------------
|
| This array contains glob patterns relative to the project's root directory.
| These patterns are used by the `registration.php` file to generate a list
| of includes for various files and resources throughout the project.
| Each glob pattern matches a specific location where Magento looks for
| registration files for modules, themes, language packs, and other
| components that need to be registered within the Magento system.
|
| The patterns below are designed to match specific paths in the app/code,
| app/design, app/i18n, lib/internal, and setup directories.
|
*/

return [
    // Matches all cli_commands.php files across modules in app/code
    joinPaths('app', 'code', '*', '*', 'cli_commands.php'),

    // Matches all registration.php files across modules in app/code
    joinPaths('app', 'code', '*', '*', 'registration.php'),

    // Matches all registration.php files across themes in app/design
    joinPaths('app', 'design', '*', '*', '*', 'registration.php'),

    // Matches all registration.php files for language packs in app/i18n
    joinPaths('app', 'i18n', '*', '*', 'registration.php'),

    // Matches all registration.php files in the internal library directory
    joinPaths('lib', 'internal', '*', '*', 'registration.php'),

    // Matches all registration.php files in subdirectories under lib/internal
    joinPaths('lib', 'internal', '*', '*', '*', 'registration.php'),

    // Matches all registration.php files in the setup source directory
    joinPaths('setup', 'src', '*', '*', 'registration.php'),
];
